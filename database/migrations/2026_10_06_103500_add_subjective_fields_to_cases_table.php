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
        if (Schema::hasTable('cases')) {
            Schema::table('cases', function (Blueprint $table) {
                if (!Schema::hasColumn('cases', 'subjective_complaint')) {
                    $table->text('subjective_complaint')->nullable()->after('category');
                }
                if (!Schema::hasColumn('cases', 'subjective_problem')) {
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
        if (Schema::hasTable('cases')) {
            Schema::table('cases', function (Blueprint $table) {
                if (Schema::hasColumn('cases', 'subjective_complaint')) {
                    $table->dropColumn('subjective_complaint');
                }
                if (Schema::hasColumn('cases', 'subjective_problem')) {
                    $table->dropColumn('subjective_problem');
                }
            });
        }
    }
};
