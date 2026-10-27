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
        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'registered_by' => null,
                'is_active' => true,
            ]
        );

        $otherRoles = [
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

        foreach ($otherRoles as $role) {
            User::updateOrCreate(
                ['email' => $role['email']],
                [
                    'name' => $role['name'],
                    'password' => Hash::make('password'),
                    'role' => $role['role'],
                    'email_verified_at' => now(),
                    'registered_by' => $admin->id,
                    'is_active' => true,
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
