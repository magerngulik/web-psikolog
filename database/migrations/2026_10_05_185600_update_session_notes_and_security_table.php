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
        // 1. Tabel user_security_pins
        if (!Schema::hasTable('user_security_pins')) {
            Schema::create('user_security_pins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('pin_hash');
                $table->timestamps();
            });
        }

        // 2. Tabel payments (Pendukung Dashboard Analytics)
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('patient_session_id')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('status', 50)->default('Paid'); // Paid, Unpaid, Pending
                $table->string('payment_method', 50)->nullable();
                $table->timestamps();
            });
        }

        // 3. Tambah sipa_number ke users
        if (!Schema::hasColumn('users', 'sipa_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('sipa_number')->nullable()->after('email');
            });
        }

        // 4. Tabel session_notes
        if (!Schema::hasTable('session_notes')) {
            Schema::create('session_notes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('appointment_id')->nullable()->index();
                $table->uuid('patient_session_id')->nullable()->index();
                $table->text('subjective')->nullable();
                $table->text('objective')->nullable();
                $table->text('assessment')->nullable();
                $table->text('plan')->nullable();
                $table->string('icd10_code', 20)->nullable();
                $table->string('icd10_description')->nullable();
                $table->json('assessment_methods')->nullable();
                $table->json('intervention_ids')->nullable();
                $table->string('follow_up_status', 50)->default('Selesai');
                $table->text('client_message')->nullable();
                $table->integer('duration_minutes')->default(60);
                $table->string('qr_code_token', 64)->unique()->nullable();
                $table->boolean('is_locked')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('session_notes', function (Blueprint $table) {
                if (!Schema::hasColumn('session_notes', 'subjective')) {
                    $table->text('subjective')->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'objective')) {
                    $table->text('objective')->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'assessment')) {
                    $table->text('assessment')->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'plan')) {
                    $table->text('plan')->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'icd10_code')) {
                    $table->string('icd10_code', 20)->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'icd10_description')) {
                    $table->string('icd10_description')->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'assessment_methods')) {
                    $table->json('assessment_methods')->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'intervention_ids')) {
                    $table->json('intervention_ids')->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'follow_up_status')) {
                    $table->string('follow_up_status', 50)->default('Selesai');
                }
                if (!Schema::hasColumn('session_notes', 'client_message')) {
                    $table->text('client_message')->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'duration_minutes')) {
                    $table->integer('duration_minutes')->default(60);
                }
                if (!Schema::hasColumn('session_notes', 'qr_code_token')) {
                    $table->string('qr_code_token', 64)->unique()->nullable();
                }
                if (!Schema::hasColumn('session_notes', 'is_locked')) {
                    $table->boolean('is_locked')->default(false);
                }
            });
        }

        // 5. Penyesuaian kolom pada patient_sessions
        Schema::table('patient_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_sessions', 'subjective')) {
                $table->text('subjective')->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'objective')) {
                $table->text('objective')->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'assessment')) {
                $table->text('assessment')->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'plan')) {
                $table->text('plan')->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'icd10_code')) {
                $table->string('icd10_code', 20)->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'icd10_description')) {
                $table->string('icd10_description')->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'assessment_methods')) {
                $table->json('assessment_methods')->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'intervention_ids')) {
                $table->json('intervention_ids')->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'follow_up_status')) {
                $table->string('follow_up_status', 50)->default('Selesai');
            }
            if (!Schema::hasColumn('patient_sessions', 'client_message')) {
                $table->text('client_message')->nullable();
            }
            if (!Schema::hasColumn('patient_sessions', 'qr_code_token')) {
                $table->string('qr_code_token', 64)->unique()->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_notes');
        Schema::dropIfExists('user_security_pins');
        Schema::dropIfExists('payments');

        if (Schema::hasColumn('users', 'sipa_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('sipa_number');
            });
        }

        Schema::table('patient_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'subjective', 'objective', 'assessment', 'plan',
                'icd10_code', 'icd10_description', 'assessment_methods',
                'intervention_ids', 'follow_up_status', 'client_message',
                'qr_code_token'
            ]);
        });
    }
};
