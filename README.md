# Kad Kahwin Digital

Platform hos jemputan perkahwinan digital untuk Malaysia. Satu aplikasi dan satu domain menempatkan semua pasangan. Setiap majlis mempunyai URL sendiri, reka bentuk sendiri, RSVP, dan buku ucapan.

Contoh:

- `https://domain-anda.com/ali-dan-fatimah`
- `https://domain-anda.com/syedsaidyestosakinah`

Hanya pentadbir platform yang log masuk. Pasangan dan tetamu tidak memerlukan akaun.

## Keperluan

- PHP 8.3 atau lebih baharu
- Composer
- Node.js dan npm
- MySQL

Projek ini menggunakan Laravel 13, Blade, Tailwind CSS, dan JavaScript biasa.

## Pemasangan

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Cipta pangkalan data MySQL, kemudian isi `DB_*` dalam `.env`.

```bash
php artisan migrate
php artisan storage:link
npm install
npm run build
php artisan serve
```

Aplikasi tempatan dibuka di `http://127.0.0.1:8000`.

## Akaun pentadbir

Tiada pendaftaran awam. Akaun dicipta melalui seeder, bukan ditulis dalam kod.

Dalam `.env`:

```env
ADMIN_NAME=Pentadbir
ADMIN_EMAIL=admin@domain-anda.com
ADMIN_PASSWORD=kata-laluan-sekurang-kurangnya-8-aksara
```

Kemudian:

```bash
php artisan db:seed
```

Log masuk di `/admin/login`.

`db:seed` tidak menukar kata laluan akaun yang sudah wujud. Data contoh majlis hanya dimasukkan apabila `APP_ENV=local`, dan rekod sedia ada tidak ditulis semula.

## Apa yang boleh diurus

Dari papan pemuka `/admin`:

- Cipta, sunting, aktifkan, dan padam kad kahwin.
- Pilih tema, warna, tipografi, dan hiasan.
- Muat naik gambar muka depan dan muzik, atau tampal pautan YouTube.
- Tetapkan hashtag majlis, contoh `#imandalamsyifa`.
- Lihat RSVP, eksport CSV, dan padam ucapan.

Tetamu membuka URL slug untuk melihat jemputan, peta, Waze, WhatsApp keluarga, borang kehadiran, dan buku ucapan. Jemputan yang tidak aktif tidak dipaparkan.

## Muzik dan muat naik

Pautan YouTube dimainkan melalui pemain YouTube selepas tetamu mengetik halaman. Fail audio terus (MP3, WAV, OGG, M4A) menggunakan pemain HTML.

Had muat naik mengikut tetapan PHP `upload_max_filesize` dan `post_max_size`. Pada mesin pembangunan semasa, hadnya 2MB.

## Pengeluaran di Oracle Cloud

Cipta VM Always Free:

1. Compute, Instances, Create instance.
2. Imej Ubuntu 24.04.
3. Shape `VM.Standard.A1.Flex` dengan 1 OCPU dan 6 GB RAM. Jika kapasiti penuh, pilih `VM.Standard.E2.1.Micro`.
4. Pastikan IPv4 awam dihidupkan dan muat turun kunci SSH.
5. Pada senarai keselamatan VCN, benarkan TCP 80 dan TCP 443 dari `0.0.0.0/0`. Port 22 sudah dibuka.

Kemudian dari komputer anda:

```bash
ssh -i kunci-oracle.key ubuntu@IP-AWAM
sudo apt-get update && sudo apt-get install -y git
git clone https://github.com/Zakrierahim/kad_kahwin_online.git
cd kad_kahwin_online
sudo bash deploy/oracle/setup.sh
```

Skrip memasang Nginx, PHP 8.3, MySQL dan Node, kemudian menjalankan migrasi. `APP_ENV=production` menyebabkan seeder mencipta akaun pentadbir sahaja. Majlis contoh tidak dimasukkan.

Jika repositori ini peribadi, clone dengan token GitHub sebelum menjalankan skrip. Jangan masukkan kata laluan ke dalam git.

## Render

Render menjalankan imej Docker dalam repositori ini. Pangkalan data percuma ialah PostgreSQL dan tamat selepas 30 hari. Laman percuma tidur selepas 15 minit tanpa pelawat. Gambar dan fail muzik yang dimuat naik hilang bila pelayan dimulakan semula. Pautan YouTube dan data dalam pangkalan data kekal.

Pilih branch `features` semasa mencipta Web Service. Tetapkan `APP_ENV=production` supaya seeder hanya mencipta akaun pentadbir.

## Ujian

```bash
php artisan test
```

Ujian menggunakan SQLite dalam memori dan tidak menyentuh pangkalan data MySQL.
