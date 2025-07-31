<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'superadmin@bps.test',
            ],
            [
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'password' => Hash::make('superbadmin'),
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'ketuatim@bps.test',
            ],
            [
                'name' => 'Ketua Tim',
                'role' => 'ketua_tim',
                'password' => Hash::make('12344321'),
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'anggota@bps.test',
            ],
            [
                'name' => 'Anggota Tim',
                'role' => 'anggota_tim',
                'password' => Hash::make('lowclass'),
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'kepala@bps.test',
            ],
            [
                'name' => 'Kepala BPS',
                'role' => 'kepala',
                'password' => Hash::make('superordinat'),
            ]
        );
    }
}
