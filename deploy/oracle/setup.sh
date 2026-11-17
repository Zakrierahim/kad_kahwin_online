#!/usr/bin/env bash
set -euo pipefail

if [[ "${EUID}" -ne 0 ]]; then
    echo "Jalankan dengan: sudo bash deploy/oracle/setup.sh"
    exit 1
fi

APP_DIR="/var/www/kad-kahwin"
REPO_URL="https://github.com/Zakrierahim/kad_kahwin_online.git"
DB_NAME="kad_kahwin_online"
DB_USER="kadkahwin"

read -r -p "Alamat awam (IP atau domain, contoh 123.45.67.89): " PUBLIC_HOST
read -r -p "Emel pentadbir: " ADMIN_EMAIL
read -r -s -p "Kata laluan pentadbir (minimum 8 aksara): " ADMIN_PASSWORD
echo
read -r -s -p "Kata laluan MySQL untuk pengguna ${DB_USER}: " DB_PASSWORD
echo

if [[ -z "${PUBLIC_HOST}" || -z "${ADMIN_EMAIL}" || ${#ADMIN_PASSWORD} -lt 8 || -z "${DB_PASSWORD}" ]]; then
    echo "Alamat, emel, kata laluan pentadbir (8+ aksara) dan kata laluan MySQL diperlukan."
    exit 1
fi

export DEBIAN_FRONTEND=noninteractive

if ! swapon --show | grep -q /swapfile; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    grep -q '/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

apt-get update
apt-get install -y ca-certificates curl git unzip nginx mysql-server software-properties-common

. /etc/os-release
if [[ "${VERSION_ID}" == "22.04" ]]; then
    add-apt-repository -y ppa:ondrej/php
    apt-get update
fi

apt-get install -y \
    php8.3-cli php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
    php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd

if ! command -v composer >/dev/null 2>&1; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

if ! command -v node >/dev/null 2>&1 || ! node -v | grep -qE '^v(2[2-9]|[3-9][0-9])'; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt-get install -y nodejs
fi

PHP_INI_DIR="/etc/php/8.3/fpm/conf.d"
cat > "${PHP_INI_DIR}/99-kadkahwin.ini" <<'EOF'
upload_max_filesize = 8M
post_max_size = 8M
memory_limit = 256M
EOF
cp "${PHP_INI_DIR}/99-kadkahwin.ini" /etc/php/8.3/cli/conf.d/99-kadkahwin.ini

iptables -C INPUT -p tcp --dport 80 -j ACCEPT 2>/dev/null || iptables -I INPUT -p tcp --dport 80 -j ACCEPT
iptables -C INPUT -p tcp --dport 443 -j ACCEPT 2>/dev/null || iptables -I INPUT -p tcp --dport 443 -j ACCEPT
if command -v netfilter-persistent >/dev/null 2>&1; then
    netfilter-persistent save
fi

SQL_PASSWORD="${DB_PASSWORD//\'/\'\'}"
mysql --protocol=socket -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${SQL_PASSWORD}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${SQL_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

if [[ ! -d "${APP_DIR}/.git" ]]; then
    mkdir -p "${APP_DIR}"
    git clone "${REPO_URL}" "${APP_DIR}"
else
    git -C "${APP_DIR}" pull --ff-only
fi

cd "${APP_DIR}"

if [[ ! -f .env ]]; then
    cp .env.example .env
fi

set_env() {
    php -r '
        $path = ".env";
        $key = $argv[1];
        $value = $argv[2];
        $exported = "\"".str_replace(["\\", "\""], ["\\\\", "\\\""], $value)."\"";
        $lines = file_exists($path) ? file($path, FILE_IGNORE_NEW_LINES) : [];
        $found = false;
        foreach ($lines as &$line) {
            if (str_starts_with($line, $key."=")) {
                $line = $key."=".$exported;
                $found = true;
            }
        }
        unset($line);
        if (!$found) {
            $lines[] = $key."=".$exported;
        }
        file_put_contents($path, implode(PHP_EOL, $lines).PHP_EOL);
    ' "$1" "$2"
}

set_env APP_NAME "Kad Kahwin"
set_env APP_ENV production
set_env APP_DEBUG false
set_env APP_URL "http://${PUBLIC_HOST}"
set_env APP_LOCALE ms
set_env DB_CONNECTION mysql
set_env DB_HOST 127.0.0.1
set_env DB_PORT 3306
set_env DB_DATABASE "${DB_NAME}"
set_env DB_USERNAME "${DB_USER}"
set_env DB_PASSWORD "${DB_PASSWORD}"
set_env SESSION_DRIVER database
set_env CACHE_STORE database
set_env QUEUE_CONNECTION database
set_env ADMIN_NAME Pentadbir
set_env ADMIN_EMAIL "${ADMIN_EMAIL}"
set_env ADMIN_PASSWORD "${ADMIN_PASSWORD}"
set_env LOG_LEVEL error

composer install --no-dev --optimize-autoloader --no-interaction
php artisan key:generate --force
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

APP_USER="ubuntu"
if ! id "${APP_USER}" >/dev/null 2>&1; then
    APP_USER="root"
fi
chown -R "${APP_USER}:www-data" "${APP_DIR}"
chmod -R ug+rwx storage bootstrap/cache

cat > /etc/nginx/sites-available/kad-kahwin <<EOF
server {
    listen 80;
    server_name ${PUBLIC_HOST};
    root ${APP_DIR}/public;
    index index.php;
    client_max_body_size 8M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sfn /etc/nginx/sites-available/kad-kahwin /etc/nginx/sites-enabled/kad-kahwin
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl enable --now nginx php8.3-fpm mysql
systemctl reload nginx
systemctl reload php8.3-fpm

if [[ ! "${PUBLIC_HOST}" =~ ^[0-9.]+$ ]]; then
    apt-get install -y certbot python3-certbot-nginx
    if certbot --nginx -d "${PUBLIC_HOST}" --non-interactive --agree-tos -m "${ADMIN_EMAIL}" --redirect; then
        set_env APP_URL "https://${PUBLIC_HOST}"
        php artisan config:cache
    else
        echo "Sijil HTTPS tidak dipasang. Pastikan domain sudah menunjuk ke IP pelayan, kemudian jalankan certbot semula."
    fi
fi

echo
echo "Siap. Buka http://${PUBLIC_HOST}"
echo "Log masuk pentadbir: http://${PUBLIC_HOST}/admin/login"
echo "Data contoh tidak dimasukkan. Akaun yang ada hanyalah pentadbir."
