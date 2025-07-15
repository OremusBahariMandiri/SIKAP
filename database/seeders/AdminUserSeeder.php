<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserAccess;

class AdminUserSeeder extends Seeder
{
    /**
     * Buat user admin dan hak akses awal.
     */
    public function run(): void
    {
        // Buat atau update user admin
        $admin = User::updateOrCreate(
            ['NikKry' => 'admin'],
            [
                'IdKode' => 'USR0001',
                'NamaKry' => 'Administrator',
                'DepartemenKry' => 'IT',
                'JabatanKry' => 'System Administrator',
                'WilkerKry' => 'Jakarta',
                'PasswordKry' => Hash::make('admin123'),
                'is_admin' => true
            ]
        );

        // Definisikan semua menu yang ada di aplikasi
        $menus = [
            'users',
            'perusahaan',
            'kategori-dok',
            'jenis-dok',
            'dokLegal'
        ];

        // Berikan akses penuh ke semua menu untuk admin
        foreach ($menus as $menu) {
            UserAccess::updateOrCreate(
                [
                    'IdKodeA01' => $admin->id,
                    'MenuAcs' => $menu
                ],
                [
                    'TambahAcs' => true,
                    'UbahAcs' => true,
                    'HapusAcs' => true,
                    'DownloadAcs' => true,
                    'DetailAcs' => true,
                    'MonitoringAcs' => true
                ]
            );
        }

        $this->command->info('Admin user created with full access to all menus.');
    }
}