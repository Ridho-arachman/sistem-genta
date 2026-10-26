<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Pengawas User',
                'email' => 'pengawas@test.com',
                'role' => 'pengawas',
            ],
            [
                'name' => 'Manajerial User',
                'email' => 'manajerial@test.com',
                'role' => 'manajerial',
            ],
            [
                'name' => 'Kepala Dapur User',
                'email' => 'kepaladapur@test.com',
                'role' => 'kepala_dapur',
            ],
        ];

        foreach ($roles as $role) {
            User::updateOrCreate(
                ['email' => $role['email']],
                [
                    'name' => $role['name'],
                    'password' => Hash::make('password'),
                    'role' => $role['role'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Role users created successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@test.com', 'password'],
                ['Pengawas', 'pengawas@test.com', 'password'],
                ['Manajerial', 'manajerial@test.com', 'password'],
                ['Kepala Dapur', 'kepaladapur@test.com', 'password'],
            ]
        );
    }
}