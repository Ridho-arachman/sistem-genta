<?php

namespace Database\Seeders;

use App\Models\Dapur;
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
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Bambang Setiawan',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'registered_by' => null,
            ],
        );

        $dapurIds = Dapur::query()->orderBy('nama')->pluck('id')->all();

        /** @var array<int, array{email: string, name: string, role: string, dapur_index: int|null, pengawas_offset: int|null}> $users */
        $users = [
            ['email' => 'manajerial@test.com', 'name' => 'Siti Nuraini', 'role' => 'manajerial', 'dapur_index' => null, 'pengawas_offset' => null],
            ['email' => 'pengawas@test.com', 'name' => 'Ahmad Fauzi', 'role' => 'pengawas', 'dapur_index' => null, 'pengawas_offset' => 0],
            ['email' => 'kepaladapur@test.com', 'name' => 'Rizky Pratama', 'role' => 'kepala_dapur', 'dapur_index' => 0, 'pengawas_offset' => null],
            ['email' => 'pengawas2@test.com', 'name' => 'Dewi Sartika', 'role' => 'pengawas', 'dapur_index' => null, 'pengawas_offset' => 2],
            ['email' => 'pengawas3@test.com', 'name' => 'Mulyadi', 'role' => 'pengawas', 'dapur_index' => null, 'pengawas_offset' => 4],
        ];

        foreach ($users as $userData) {
            $user = User::query()->firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                    'registered_by' => $admin->id,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                    'dapur_id' => $userData['dapur_index'] === null
                        ? null
                        : ($dapurIds[$userData['dapur_index']] ?? null),
                ],
            );

            if ($userData['pengawas_offset'] !== null) {
                $user->dapurs()->syncWithoutDetaching(array_slice($dapurIds, $userData['pengawas_offset'], 2));
            }
        }

        $this->command->info('Users seeded successfully!');
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
