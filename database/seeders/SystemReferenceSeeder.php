<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Gender
            ['group_key' => 'gender', 'label' => 'Laki-laki', 'value' => 'Laki-laki', 'sort_order' => 1],
            ['group_key' => 'gender', 'label' => 'Perempuan', 'value' => 'Perempuan', 'sort_order' => 2],

            // Case Category
            ['group_key' => 'case_category', 'label' => 'Anak & Remaja', 'value' => 'Anak & Remaja', 'sort_order' => 1],
            ['group_key' => 'case_category', 'label' => 'Dewasa', 'value' => 'Dewasa', 'sort_order' => 2],
            ['group_key' => 'case_category', 'label' => 'Pernikahan & Keluarga', 'value' => 'Pernikahan & Keluarga', 'sort_order' => 3],
            ['group_key' => 'case_category', 'label' => 'Karir & Pekerjaan', 'value' => 'Karir & Pekerjaan', 'sort_order' => 4],
            ['group_key' => 'case_category', 'label' => 'Lainnya', 'value' => 'Lainnya', 'sort_order' => 5],

            // Follow Up Type
            ['group_key' => 'follow_up_type', 'label' => 'Kontrol Rutin', 'value' => 'routine_control', 'sort_order' => 1],
            ['group_key' => 'follow_up_type', 'label' => 'Rujukan Eksternal (Psikiater/RS)', 'value' => 'external_referral', 'sort_order' => 2],
            ['group_key' => 'follow_up_type', 'label' => 'Terapi Selesai (Finished)', 'value' => 'finished', 'sort_order' => 3],

            // Payment Method
            ['group_key' => 'payment_method', 'label' => 'Transfer Bank', 'value' => 'transfer', 'sort_order' => 1],
            ['group_key' => 'payment_method', 'label' => 'Tunai (Cash)', 'value' => 'cash', 'sort_order' => 2],
            ['group_key' => 'payment_method', 'label' => 'QRIS', 'value' => 'qris', 'sort_order' => 3],
        ];

        foreach ($data as $item) {
            DB::table('system_references')->updateOrInsert(
                ['group_key' => $item['group_key'], 'value' => $item['value']],
                [
                    'id' => Str::uuid()->toString(),
                    'label' => $item['label'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}