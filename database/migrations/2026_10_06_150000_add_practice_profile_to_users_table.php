<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'title_prefix')) {
                $table->string('title_prefix')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'title_suffix')) {
                $table->string('title_suffix')->nullable()->after('title_prefix');
            }
            if (!Schema::hasColumn('users', 'practice_name')) {
                $table->string('practice_name')->default('MANDIRI')->after('sipa_number');
            }
            if (!Schema::hasColumn('users', 'practice_city')) {
                $table->string('practice_city')->default('Selatpanjang')->after('practice_name');
            }
            if (!Schema::hasColumn('users', 'practice_address')) {
                $table->text('practice_address')->nullable()->after('practice_city');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('practice_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'title_prefix',
                'title_suffix',
                'practice_name',
                'practice_city',
                'practice_address',
                'phone',
            ]);
        });
    }
};

