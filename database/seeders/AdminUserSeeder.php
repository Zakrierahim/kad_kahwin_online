<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'Pentadbir');

        if (! is_string($email) || ! is_string($password) || $email === '' || $password === '') {
            $this->command?->warn('ADMIN_EMAIL dan ADMIN_PASSWORD tidak ditetapkan. Akaun pentadbir tidak dicipta.');

            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $this->command?->warn('Kelayakan pentadbir tidak sah. Emel mesti sah dan kata laluan sekurang-kurangnya 8 aksara.');

            return;
        }

        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => is_string($name) && $name !== '' ? $name : 'Pentadbir',
                'password' => $password,
                'email_verified_at' => now(),
            ],
        );
    }
}
