<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('group_key'); // 'gender', 'case_category', 'follow_up_type', 'payment_method'
            $table->string('label');     // Teks Tampilan (misal: "Rujuk ke Psikiater")
            $table->string('value');     // Value DB (misal: "external_referral")
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_references');
    }
};