<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Administrator',
                'email'    => 'admin@isp.local',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Operator NOC',
                'email'    => 'operator@isp.local',
                'password' => Hash::make('operator123'),
                'role'     => 'operator',
            ],
            [
                'name'     => 'Budi Santoso',
                'email'    => 'pelanggan@isp.local',
                'password' => Hash::make('pelanggan123'),
                'role'     => 'pelanggan',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                $u
            );
        }
    }
}
