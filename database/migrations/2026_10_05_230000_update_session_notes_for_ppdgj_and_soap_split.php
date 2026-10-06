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
        // 1. Kolom tambahan pada tabel session_notes
        if (Schema::hasTable('session_notes')) {
            Schema::table('session_notes', function (Blueprint $table) {
                if (!Schema::hasColumn('session_notes', 'subjective_complaint')) {
                    $table->text('subjective_complaint')->nullable()->after('subjective');
                }
                if (!Schema::hasColumn('session_notes', 'subjective_problem')) {
                    $table->text('subjective_problem')->nullable()->after('subjective_complaint');
                }
            });
        }

        // 2. Kolom tambahan pada tabel patient_sessions
        if (Schema::hasTable('patient_sessions')) {
            Schema::table('patient_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_sessions', 'subjective_complaint')) {
                    $table->text('subjective_complaint')->nullable()->after('subjective');
                }
                if (!Schema::hasColumn('patient_sessions', 'subjective_problem')) {
                    $table->text('subjective_problem')->nullable()->after('subjective_complaint');
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
                $table->dropColumn(['subjective_complaint', 'subjective_problem']);
            });
        }

        if (Schema::hasTable('patient_sessions')) {
            Schema::table('patient_sessions', function (Blueprint $table) {
                $table->dropColumn(['subjective_complaint', 'subjective_problem']);
            });
        }
    }
};
