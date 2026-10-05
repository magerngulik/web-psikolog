<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->onDelete('cascade');
            $table->uuid('medical_case_id')->nullable();
            $table->integer('session_number');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->default(60);
            $table->string('status')->default('scheduled'); // scheduled, confirmed, in_progress, done, cancelled
            
            // Clinical Notes
            $table->text('complaint')->nullable();
            $table->text('summary')->nullable();
            $table->text('dynamic_notes')->nullable();
            $table->text('intervention_notes')->nullable();
            $table->text('result')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('next_plan')->nullable();
            
            // Legacy Options
            $table->string('follow_up_type')->nullable(); // routine_control, external_referral, finished
            $table->text('follow_up_note')->nullable();
            $table->text('special_note')->nullable();
            $table->text('message')->nullable();
            
            // Lock & Fee
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->decimal('fee', 12, 2)->default(0);
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, waived
            $table->string('payment_method')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_sessions');
    }
};
