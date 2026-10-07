<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('activity_code')->unique(); // e.g. ACT-202610-001
            $table->foreignUuid('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('title');
            $table->string('event_type')->default('seminar'); // seminar, webinar, workshop, psychoeducation, talkshow, training, other
            $table->string('role')->default('keynote_speaker'); // keynote_speaker, co_speaker, facilitator, moderator, assessor, other
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('delivery_mode')->default('offline'); // offline, online, hybrid
            $table->string('location_venue')->nullable();
            $table->string('organizer_name')->nullable();
            $table->string('event_pic_name')->nullable();
            $table->string('event_pic_phone')->nullable();
            $table->string('target_audience')->nullable();
            $table->integer('estimated_audience')->nullable();
            $table->decimal('fee', 12, 2)->default(0);
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, waived_pro_bono
            $table->string('payment_method')->nullable(); // cash, transfer, qris
            $table->decimal('skp_points', 5, 2)->nullable();
            $table->text('summary_notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

