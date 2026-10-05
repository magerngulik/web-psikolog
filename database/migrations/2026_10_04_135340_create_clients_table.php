<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('client_code')->unique(); // CLI-2026-0001
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            
            // Kolom detail legacy
            $table->string('nik')->nullable();
            $table->integer('birth_order')->nullable();
            $table->string('last_education')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('occupation')->nullable();
            $table->boolean('is_student')->default(false);
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};