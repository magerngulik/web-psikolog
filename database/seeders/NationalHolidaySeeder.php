<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NationalHolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            // 2026
            ['holiday_date' => '2026-01-01', 'holiday_name' => 'Tahun Baru 2026 Masehi', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-01-16', 'holiday_name' => 'Isra Mikraj Nabi Muhammad SAW', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-02-17', 'holiday_name' => 'Tahun Baru Imlek 2577 Kongzili', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-02-18', 'holiday_name' => 'Cuti Bersama Tahun Baru Imlek', 'is_cuti_bersama' => true],
            ['holiday_date' => '2026-03-19', 'holiday_name' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-03-20', 'holiday_name' => 'Hari Raya Idul Fitri 1447 H', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-03-21', 'holiday_name' => 'Hari Raya Idul Fitri 1447 H', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-03-23', 'holiday_name' => 'Cuti Bersama Idul Fitri 1447 H', 'is_cuti_bersama' => true],
            ['holiday_date' => '2026-03-24', 'holiday_name' => 'Cuti Bersama Idul Fitri 1447 H', 'is_cuti_bersama' => true],
            ['holiday_date' => '2026-04-03', 'holiday_name' => 'Wafat Yesus Kristus', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-04-05', 'holiday_name' => 'Hari Paskah', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-05-01', 'holiday_name' => 'Hari Buruh Internasional', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-05-14', 'holiday_name' => 'Kenaikan Yesus Kristus', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-05-27', 'holiday_name' => 'Hari Raya Idul Adha 1447 H', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-05-31', 'holiday_name' => 'Hari Raya Waisak 2570 BE', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-06-01', 'holiday_name' => 'Hari Lahir Pancasila', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-06-16', 'holiday_name' => 'Tahun Baru Islam 1448 H', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-08-17', 'holiday_name' => 'Hari Kemerdekaan RI', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-08-25', 'holiday_name' => 'Maulid Nabi Muhammad SAW', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-12-25', 'holiday_name' => 'Hari Raya Natal', 'is_cuti_bersama' => false],
            ['holiday_date' => '2026-12-26', 'holiday_name' => 'Cuti Bersama Hari Raya Natal', 'is_cuti_bersama' => true],

            // 2027
            ['holiday_date' => '2027-01-01', 'holiday_name' => 'Tahun Baru 2027 Masehi', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-01-06', 'holiday_name' => 'Isra Mikraj Nabi Muhammad SAW', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-02-06', 'holiday_name' => 'Tahun Baru Imlek 2578 Kongzili', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-03-08', 'holiday_name' => 'Hari Suci Nyepi Saka 1949', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-03-10', 'holiday_name' => 'Hari Raya Idul Fitri 1448 H', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-03-11', 'holiday_name' => 'Hari Raya Idul Fitri 1448 H', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-03-12', 'holiday_name' => 'Cuti Bersama Idul Fitri 1448 H', 'is_cuti_bersama' => true],
            ['holiday_date' => '2027-03-26', 'holiday_name' => 'Wafat Yesus Kristus', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-03-28', 'holiday_name' => 'Hari Paskah', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-05-01', 'holiday_name' => 'Hari Buruh Internasional', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-05-06', 'holiday_name' => 'Kenaikan Yesus Kristus', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-05-17', 'holiday_name' => 'Hari Raya Idul Adha 1448 H', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-05-20', 'holiday_name' => 'Hari Raya Waisak 2571 BE', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-06-01', 'holiday_name' => 'Hari Lahir Pancasila', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-06-06', 'holiday_name' => 'Tahun Baru Islam 1449 H', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-08-15', 'holiday_name' => 'Maulid Nabi Muhammad SAW', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-08-17', 'holiday_name' => 'Hari Kemerdekaan RI', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-12-25', 'holiday_name' => 'Hari Raya Natal', 'is_cuti_bersama' => false],
            ['holiday_date' => '2027-12-27', 'holiday_name' => 'Cuti Bersama Hari Raya Natal', 'is_cuti_bersama' => true],
        ];

        foreach ($holidays as $holiday) {
            DB::table('national_holidays')->updateOrInsert(
                ['holiday_date' => $holiday['holiday_date']],
                [
                    'id' => Str::uuid()->toString(),
                    'holiday_name' => $holiday['holiday_name'],
                    'is_cuti_bersama' => $holiday['is_cuti_bersama'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}