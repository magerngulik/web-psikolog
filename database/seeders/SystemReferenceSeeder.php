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

            // Psychological Intervention (1-16)
            ['group_key' => 'psychological_intervention', 'label' => 'Psikoedukasi', 'value' => '1', 'sort_order' => 1],
            ['group_key' => 'psychological_intervention', 'label' => 'Konseling Psikologis', 'value' => '2', 'sort_order' => 2],
            ['group_key' => 'psychological_intervention', 'label' => 'Cognitive Behavioral Therapy (CBT)', 'value' => '3', 'sort_order' => 3],
            ['group_key' => 'psychological_intervention', 'label' => 'Acceptance & Commitment Therapy (ACT)', 'value' => '4', 'sort_order' => 4],
            ['group_key' => 'psychological_intervention', 'label' => 'Behavioral Activation', 'value' => '5', 'sort_order' => 5],
            ['group_key' => 'psychological_intervention', 'label' => 'Mindfulness & Relaxation Therapy', 'value' => '6', 'sort_order' => 6],
            ['group_key' => 'psychological_intervention', 'label' => 'Solution-Focused Brief Therapy (SFBT)', 'value' => '7', 'sort_order' => 7],
            ['group_key' => 'psychological_intervention', 'label' => 'Client-Centered Therapy / Humanistic', 'value' => '8', 'sort_order' => 8],
            ['group_key' => 'psychological_intervention', 'label' => 'Interpersonal Psychotherapy (IPT)', 'value' => '9', 'sort_order' => 9],
            ['group_key' => 'psychological_intervention', 'label' => 'Psychodynamic / Psychoanalytic', 'value' => '10', 'sort_order' => 10],
            ['group_key' => 'psychological_intervention', 'label' => 'Family / Systemic Therapy', 'value' => '11', 'sort_order' => 11],
            ['group_key' => 'psychological_intervention', 'label' => 'Couples / Marital Therapy', 'value' => '12', 'sort_order' => 12],
            ['group_key' => 'psychological_intervention', 'label' => 'Expressive / Art / Play Therapy', 'value' => '13', 'sort_order' => 13],
            ['group_key' => 'psychological_intervention', 'label' => 'Crisis Intervention & Safety Planning', 'value' => '14', 'sort_order' => 14],
            ['group_key' => 'psychological_intervention', 'label' => 'Motivational Interviewing (MI)', 'value' => '15', 'sort_order' => 15],
            ['group_key' => 'psychological_intervention', 'label' => 'Biofeedback / Neurofeedback', 'value' => '16', 'sort_order' => 16],

            // Clinical Diagnosis Tambahan
            ['group_key' => 'clinical_diagnosis', 'label' => 'Burnout Kronis & Kelelahan Emosional Kerja', 'value' => 'Z73.0', 'sort_order' => 1],
            ['group_key' => 'clinical_diagnosis', 'label' => 'Duka Cita & Kehilangan Mendalam (Grief & Bereavement)', 'value' => 'Z63.4', 'sort_order' => 2],
            ['group_key' => 'clinical_diagnosis', 'label' => 'Krisis Eksistensial & Quarter-life Crisis', 'value' => 'F99.0', 'sort_order' => 3],
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