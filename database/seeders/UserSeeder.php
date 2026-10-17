<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Basirah',
                'title_prefix' => '',
                'title_suffix' => 'S.Psi., M.Psi., Psikolog',
                'sipa_number' => '503/123-SIPPK/2026',
                'practice_name' => 'MANDIRI',
                'practice_city' => 'Selatpanjang',
                'password' => Hash::make('password'),
            ]
        );
    }
}

