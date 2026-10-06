<?php

namespace App\Services;

use App\Models\SystemReference;

class PpdgjCatalog
{
    /**
     * Daftar Kode & Deskripsi Diagnosis PPDGJ-III / ICD-10 untuk Praktik Psikologi & Psikiatri
     */
    public static function all(): array
    {
        $custom = [];
        try {
            $custom = SystemReference::where('group_key', 'clinical_diagnosis')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($item) => [
                    'code' => $item->value,
                    'name' => $item->label,
                    'category' => 'Master Referensi / Tambahan',
                ])
                ->toArray();
        } catch (\Throwable $e) {
            $custom = [];
        }

        return array_merge($custom, self::standardCatalog());
    }

    /**
     * Katalog Standar PPDGJ-III / ICD-10
     */
    public static function standardCatalog(): array
    {
        return [
            // F40-F48: Gangguan Neurotik, Gangguan Terkait Stres, & Gangguan Somatoform
            ['code' => 'F41.1', 'name' => 'Gangguan Kecemasan Menyeluruh (Generalized Anxiety Disorder)', 'category' => 'Anxiety & Stress'],
            ['code' => 'F41.0', 'name' => 'Gangguan Panik / Serangan Panik (Panic Disorder)', 'category' => 'Anxiety & Stress'],
            ['code' => 'F41.2', 'name' => 'Gangguan Campuran Cemas dan Depresi (Mixed Anxiety-Depression)', 'category' => 'Anxiety & Stress'],
            ['code' => 'F40.0', 'name' => 'Agorafobia (Agoraphobia)', 'category' => 'Anxiety & Stress'],
            ['code' => 'F40.1', 'name' => 'Fobia Sosial / Kecemasan Sosial (Social Anxiety Disorder)', 'category' => 'Anxiety & Stress'],
            ['code' => 'F40.2', 'name' => 'Fobia Khas / Spesifik (Specific Phobia)', 'category' => 'Anxiety & Stress'],
            ['code' => 'F42.0', 'name' => 'Gangguan Obsesif-Kompulsif Terutama Pikiran Obsesif', 'category' => 'OCD & Related'],
            ['code' => 'F42.1', 'name' => 'Gangguan Obsesif-Kompulsif Terutama Perilaku Kompulsif', 'category' => 'OCD & Related'],
            ['code' => 'F42.2', 'name' => 'Gangguan Campuran Pikiran dan Tindakan Obsesif-Kompulsif (OCD)', 'category' => 'OCD & Related'],
            ['code' => 'F43.0', 'name' => 'Reaksi Stres Akut (Acute Stress Reaction)', 'category' => 'Trauma & Stress'],
            ['code' => 'F43.1', 'name' => 'Gangguan Stres Pasca Trauma (PTSD)', 'category' => 'Trauma & Stress'],
            ['code' => 'F43.2', 'name' => 'Gangguan Penyesuaian (Adjustment Disorder)', 'category' => 'Trauma & Stress'],
            ['code' => 'F45.0', 'name' => 'Gangguan Somatisasi (Somatization Disorder)', 'category' => 'Somatoform'],
            ['code' => 'F45.2', 'name' => 'Gangguan Hipokondrik / Cemas Penyakit (Hypochondriasis)', 'category' => 'Somatoform'],
            ['code' => 'F48.0', 'name' => 'Neurastenia / Kelelahan Mental Kronis (Neurasthenia)', 'category' => 'Somatoform'],

            // F30-F39: Gangguan Suasana Perasaan / Mood (Afektif)
            ['code' => 'F32.0', 'name' => 'Episode Depresi Ringan (Mild Depressive Episode)', 'category' => 'Depression & Mood'],
            ['code' => 'F32.1', 'name' => 'Episode Depresi Sedang (Moderate Depressive Episode)', 'category' => 'Depression & Mood'],
            ['code' => 'F32.2', 'name' => 'Episode Depresi Berat Tanpa Gejala Psikotik', 'category' => 'Depression & Mood'],
            ['code' => 'F32.3', 'name' => 'Episode Depresi Berat Dengan Gejala Psikotik', 'category' => 'Depression & Mood'],
            ['code' => 'F33.0', 'name' => 'Gangguan Depresi Berulang, Episode Kini Ringan', 'category' => 'Depression & Mood'],
            ['code' => 'F33.1', 'name' => 'Gangguan Depresi Berulang, Episode Kini Sedang', 'category' => 'Depression & Mood'],
            ['code' => 'F34.1', 'name' => 'Distimia / Depresi Kronis (Dysthymia)', 'category' => 'Depression & Mood'],
            ['code' => 'F34.0', 'name' => 'Siklotimia (Cyclothymia)', 'category' => 'Bipolar & Mood'],
            ['code' => 'F31.0', 'name' => 'Gangguan Afektif Bipolar, Episode Kini Hipomanik', 'category' => 'Bipolar & Mood'],
            ['code' => 'F31.1', 'name' => 'Gangguan Afektif Bipolar, Episode Kini Maniak', 'category' => 'Bipolar & Mood'],
            ['code' => 'F31.3', 'name' => 'Gangguan Afektif Bipolar, Episode Kini Depresi', 'category' => 'Bipolar & Mood'],

            // F50-F59: Gangguan Perilaku Berhubungan dengan Faktor Fisiologis & Fisik
            ['code' => 'F50.0', 'name' => 'Anoreksia Nervosa (Anorexia Nervosa)', 'category' => 'Eating Disorders'],
            ['code' => 'F50.2', 'name' => 'Bulimia Nervosa (Bulimia Nervosa)', 'category' => 'Eating Disorders'],
            ['code' => 'F51.0', 'name' => 'Insomnia Non-organik / Gangguan Sulit Tidur', 'category' => 'Sleep Disorders'],
            ['code' => 'F51.1', 'name' => 'Hipersomnia Non-organik / Tidur Berlebihan', 'category' => 'Sleep Disorders'],
            ['code' => 'F51.4', 'name' => 'Teror Tidur / Night Terrors', 'category' => 'Sleep Disorders'],
            ['code' => 'F51.5', 'name' => 'Mimpi Buruk Berulang (Nightmares)', 'category' => 'Sleep Disorders'],
            ['code' => 'F52.0', 'name' => 'Disfungsi Seksual Non-organik', 'category' => 'Sexual Function'],

            // F60-F69: Gangguan Kepribadian & Perilaku Masa Dewasa
            ['code' => 'F60.0', 'name' => 'Gangguan Kepribadian Paranoid', 'category' => 'Personality'],
            ['code' => 'F60.1', 'name' => 'Gangguan Kepribadian Skizoid', 'category' => 'Personality'],
            ['code' => 'F60.2', 'name' => 'Gangguan Kepribadian Disosial / Antisosial', 'category' => 'Personality'],
            ['code' => 'F60.3', 'name' => 'Gangguan Kepribadian Ambang / Borderline (BPD)', 'category' => 'Personality'],
            ['code' => 'F60.4', 'name' => 'Gangguan Kepribadian Histrionik', 'category' => 'Personality'],
            ['code' => 'F60.5', 'name' => 'Gangguan Kepribadian Anankastik / Obsesif-Kompulsif (OCPD)', 'category' => 'Personality'],
            ['code' => 'F60.6', 'name' => 'Gangguan Kepribadian Menghindar (Avoidant)', 'category' => 'Personality'],
            ['code' => 'F60.7', 'name' => 'Gangguan Kepribadian Dependen', 'category' => 'Personality'],

            // F80-F89 & F90-F98: Perkembangan Anak & Remaja
            ['code' => 'F90.0', 'name' => 'Gangguan Aktivitas & Perhatian / GPPH (ADHD)', 'category' => 'Child & Adolescent'],
            ['code' => 'F91.0', 'name' => 'Gangguan Tingkah Laku Lingkungan Keluarga', 'category' => 'Child & Adolescent'],
            ['code' => 'F91.3', 'name' => 'Gangguan Sikap Menentang (Oppositional Defiant Disorder / ODD)', 'category' => 'Child & Adolescent'],
            ['code' => 'F93.0', 'name' => 'Gangguan Cemas Perpisahan Masa Kanak (Separation Anxiety)', 'category' => 'Child & Adolescent'],
            ['code' => 'F84.0', 'name' => 'Autisme Masa Kanak (Childhood Autism)', 'category' => 'Child & Adolescent'],
            ['code' => 'F84.5', 'name' => 'Sindrom Asperger (Asperger Syndrome)', 'category' => 'Child & Adolescent'],
            ['code' => 'F81.0', 'name' => 'Gangguan Membaca Khas / Disleksia', 'category' => 'Child & Adolescent'],
            ['code' => 'F81.2', 'name' => 'Gangguan Berhitung Khas / Diskalkulia', 'category' => 'Child & Adolescent'],
            ['code' => 'F98.0', 'name' => 'Enuresis Non-organik (Mengompol)', 'category' => 'Child & Adolescent'],
            ['code' => 'F98.5', 'name' => 'Gagap / Stuttering', 'category' => 'Child & Adolescent'],

            // F20-F29: Gangguan Psikotik & Skizofrenia
            ['code' => 'F20.0', 'name' => 'Skizofrenia Paranoid', 'category' => 'Psychotic'],
            ['code' => 'F20.1', 'name' => 'Skizofrenia Hebefrenik', 'category' => 'Psychotic'],
            ['code' => 'F20.3', 'name' => 'Skizofrenia Tak Terinci', 'category' => 'Psychotic'],
            ['code' => 'F23.0', 'name' => 'Gangguan Psikotik Akut dan Sementara', 'category' => 'Psychotic'],

            // F10-F19: Zat & Adiksi
            ['code' => 'F10.1', 'name' => 'Penggunaan Alkohol yang Merugikan', 'category' => 'Substance & Addiction'],
            ['code' => 'F10.2', 'name' => 'Sindrom Ketergantungan Alkohol', 'category' => 'Substance & Addiction'],
            ['code' => 'F19.1', 'name' => 'Penyalahgunaan Napza / Multi-Zat Merugikan', 'category' => 'Substance & Addiction'],
            ['code' => 'F19.2', 'name' => 'Ketergantungan Napza / Multi-Zat', 'category' => 'Substance & Addiction'],

            // Z Codes: Masalah Psikososial & Konseling Umum
            ['code' => 'Z65.9', 'name' => 'Masalah Lingkungan Psikososial & Stres Kehidupan', 'category' => 'Psychosocial Issues'],
            ['code' => 'Z63.0', 'name' => 'Masalah Relasi Pasangan / Konflik Perkawinan', 'category' => 'Psychosocial Issues'],
            ['code' => 'Z56.0', 'name' => 'Masalah Pekerjaan / Burnout / Pengangguran', 'category' => 'Psychosocial Issues'],
            ['code' => 'Z55.9', 'name' => 'Masalah Akademik & Pendidikan', 'category' => 'Psychosocial Issues'],
        ];
    }

    /**
     * Cari diagnosis PPDGJ-III berdasarkan kode atau teks deskripsi.
     */
    public static function search(?string $query = null, int $limit = 20): array
    {
        $all = self::all();

        if (empty($query)) {
            return array_slice($all, 0, $limit);
        }

        $query = strtolower(trim($query));

        $filtered = array_filter($all, function ($item) use ($query) {
            return str_contains(strtolower($item['code']), $query)
                || str_contains(strtolower($item['name']), $query)
                || str_contains(strtolower($item['category']), $query);
        });

        return array_slice(array_values($filtered), 0, $limit);
    }
}
