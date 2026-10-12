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
        // 1. Cases table indexes
        Schema::table('cases', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('status');
            $table->index('category');
            $table->index('deleted_at');
        });

        // 2. Patient Sessions table indexes
        Schema::table('patient_sessions', function (Blueprint $table) {
            $table->index('case_id');
            $table->index('medical_case_id');
            $table->index('session_date');
            $table->index('status');
            $table->index('payment_status');
            $table->index('is_locked');
            $table->index('deleted_at');
        });

        // 3. System References table indexes
        Schema::table('system_references', function (Blueprint $table) {
            $table->index('type');
            $table->index('order_column');
        });

        // 4. Security Keys table indexes
        Schema::table('security_keys', function (Blueprint $table) {
            $table->index('is_active');
        });

        // 5. Payments table indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status');
            $table->index('patient_session_id');
        });

        // 6. Activities table indexes
        Schema::table('activities', function (Blueprint $table) {
            $table->index('organization_id');
            $table->index('start_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['category']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('patient_sessions', function (Blueprint $table) {
            $table->dropIndex(['case_id']);
            $table->dropIndex(['medical_case_id']);
            $table->dropIndex(['session_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['is_locked']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('system_references', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['order_column']);
        });

        Schema::table('security_keys', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['patient_session_id']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['status']);
        });
    }
};

