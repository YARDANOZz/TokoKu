<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // 1. OWNER ACCOUNTS
            [
                'name' => 'Budi Santoso (Owner)',
                'email' => 'owner@tokoku.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081234567891',
                'address' => 'Jl. Sudirman No. 10, Jakarta Pusat',
            ],
            [
                'name' => 'Zaki Yardan Siregar (Owner Utama)',
                'email' => 'zaki@tokoku.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081299887766',
                'address' => 'Komplek Pertokoan TokoKu Blok A1, Jakarta',
            ],

            // 2. STAFF / ADMIN ACCOUNTS
            [
                'name' => 'Siti Rahma (Staff Kasir)',
                'email' => 'staff@tokoku.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '081234567892',
                'address' => 'Jl. Kebon Jeruk No. 45, Jakarta Barat',
            ],
            [
                'name' => 'Admin Operasional Toko',
                'email' => 'admin@tokoku.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '081344556677',
                'address' => 'Jl. Kemanggisan Raya No. 12, Jakarta Barat',
            ],

            // 3. USER / PEMBELI ACCOUNTS
            [
                'name' => 'Andi Wijaya (Pelanggan)',
                'email' => 'user@tokoku.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081234567893',
                'address' => 'Jl. Melati No. 8, Tangerang',
            ],
            [
                'name' => 'Rina Marlina (Pelanggan Regular)',
                'email' => 'pelanggan@tokoku.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081511223344',
                'address' => 'Jl. Anggrek No. 20, Depok',
            ],
            [
                'name' => 'Dewi Lestari (Pelanggan VIP)',
                'email' => 'pembeli@tokoku.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081622334455',
                'address' => 'Jl. Cempaka Putih No. 15, Jakarta Pusat',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
