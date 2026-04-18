<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $kasirRole = Role::where('name', 'kasir')->first();

        // Gunakan APP_URL untuk domain email default, fallback ke example.com
        $domain = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'example.com';
        // Hapus port jika ada (misal localhost:8000 → localhost)
        $domain = explode(':', $domain)[0];
        // Jika masih localhost, pakai placeholder yang lebih bersih
        if ($domain === 'localhost') {
            $domain = 'admin.test';
        }

        // 1. Akun Admin Utama
        User::firstOrCreate(
            ['email' => "admin@{$domain}"],
            [
                'name'      => 'Admin Utama',
                'password'  => Hash::make('password'),
                'role_id'   => $adminRole->id,
                'is_active' => true,
            ]
        );

        // 2. Akun Kasir Contoh
        User::firstOrCreate(
            ['email' => "kasir@{$domain}"],
            [
                'name'      => 'Kasir Demo',
                'password'  => Hash::make('password'),
                'role_id'   => $kasirRole->id,
                'is_active' => true,
            ]
        );
    }
}
