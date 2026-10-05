<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SecurityKeySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('security_keys')->updateOrInsert(
            ['is_active' => true],
            [
                'id' => Str::uuid()->toString(),
                'pin_hash' => Hash::make('123456'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}

