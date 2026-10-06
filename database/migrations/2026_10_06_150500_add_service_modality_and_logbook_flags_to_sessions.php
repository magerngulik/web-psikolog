<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_sessions', 'service_modality')) {
                $table->string('service_modality')->default('individual_direct')->after('duration_minutes');
            }
            if (!Schema::hasColumn('patient_sessions', 'is_high_risk')) {
                $table->boolean('is_high_risk')->default(false)->after('service_modality');
            }
            if (!Schema::hasColumn('patient_sessions', 'generates_report')) {
                $table->boolean('generates_report')->default(true)->after('is_high_risk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patient_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'service_modality',
                'is_high_risk',
                'generates_report',
            ]);
        });
    }
};

