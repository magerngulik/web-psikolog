<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('case_code')->nullable()->unique(); // CAS-202610-001
            $table->foreignUuid('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('title');
            $table->string('category'); // Mengacu ke system_references / kategori
            $table->text('complaint')->nullable();
            $table->text('goal')->nullable();
            $table->text('progress_note')->nullable();
            $table->string('status')->default('active'); // active, on_hold, completed, cancelled
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
