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
                if (!Schema::hasColumn('session_notes', 'intervention_notes')) {
                    $table->text('intervention_notes')->nullable()->after('intervention_ids');
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
                $table->dropColumn('intervention_notes');
            });
        }
    }
};
