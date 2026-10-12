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
        Schema::create('app_licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('machine_id')->index();
            $table->string('client_name');
            $table->text('license_token');
            $table->string('license_type')->default('lifetime');
            $table->text('signature');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('activated_at');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_licenses');
    }
};

