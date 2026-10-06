<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_logbook_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('year');
            $table->string('activity_key'); // e.g. individual, group, phone, chat_text, legal_visum, legal_witness, legal_court_report, high_risk, report
            $table->integer('month'); // 1 .. 12
            $table->integer('adjustment_count')->default(0); // additional count
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['year', 'activity_key', 'month'], 'year_activity_month_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_logbook_adjustments');
    }
};

