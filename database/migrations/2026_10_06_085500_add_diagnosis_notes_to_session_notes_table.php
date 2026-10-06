<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('session_notes')) {
            Schema::table('session_notes', function (Blueprint $table) {
                if (!Schema::hasColumn('session_notes', 'diagnosis_notes')) {
                    $table->text('diagnosis_notes')->nullable()->after('icd10_description');
                }
            });
        }

        if (Schema::hasTable('patient_sessions')) {
            Schema::table('patient_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_sessions', 'diagnosis_notes')) {
                    $table->text('diagnosis_notes')->nullable()->after('icd10_description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('session_notes')) {
            Schema::table('session_notes', function (Blueprint $table) {
                $table->dropColumn('diagnosis_notes');
            });
        }

        if (Schema::hasTable('patient_sessions')) {
            Schema::table('patient_sessions', function (Blueprint $table) {
                $table->dropColumn('diagnosis_notes');
            });
        }
    }
};
