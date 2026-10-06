<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'total_siblings')) {
                $table->integer('total_siblings')->nullable()->after('birth_order');
            }
            if (!Schema::hasColumn('clients', 'is_disabled')) {
                $table->boolean('is_disabled')->default(false)->after('total_siblings');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'total_siblings')) {
                $table->dropColumn('total_siblings');
            }
            if (Schema::hasColumn('clients', 'is_disabled')) {
                $table->dropColumn('is_disabled');
            }
        });
    }
};
