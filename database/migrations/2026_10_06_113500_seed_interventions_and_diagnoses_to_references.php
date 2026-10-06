<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $interventions = [
            1 => 'Psikoedukasi',
            2 => 'Konseling Psikologis',
            3 => 'Cognitive Behavioral Therapy (CBT)',
            4 => 'Acceptance & Commitment Therapy (ACT)',
            5 => 'Behavioral Activation',
            6 => 'Mindfulness & Relaxation Therapy',
            7 => 'Solution-Focused Brief Therapy (SFBT)',
            8 => 'Client-Centered Therapy / Humanistic',
            9 => 'Interpersonal Psychotherapy (IPT)',
            10 => 'Psychodynamic / Psychoanalytic',
            11 => 'Family / Systemic Therapy',
            12 => 'Couples / Marital Therapy',
            13 => 'Expressive / Art / Play Therapy',
            14 => 'Crisis Intervention & Safety Planning',
            15 => 'Motivational Interviewing (MI)',
            16 => 'Biofeedback / Neurofeedback',
        ];

        foreach ($interventions as $id => $name) {
            DB::table('system_references')->updateOrInsert(
                [
                    'group_key' => 'psychological_intervention',
                    'value' => (string) $id,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'label' => $name,
                    'sort_order' => $id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $diagnoses = [
            ['code' => 'Z73.0', 'name' => 'Burnout Kronis & Kelelahan Emosional Kerja', 'sort' => 1],
            ['code' => 'Z63.4', 'name' => 'Duka Cita & Kehilangan Mendalam (Grief & Bereavement)', 'sort' => 2],
            ['code' => 'F99.0', 'name' => 'Krisis Eksistensial & Quarter-life Crisis', 'sort' => 3],
        ];

        foreach ($diagnoses as $diag) {
            DB::table('system_references')->updateOrInsert(
                [
                    'group_key' => 'clinical_diagnosis',
                    'value' => $diag['code'],
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'label' => $diag['name'],
                    'sort_order' => $diag['sort'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_references')
            ->whereIn('group_key', ['psychological_intervention', 'clinical_diagnosis'])
            ->delete();
    }
};
