<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SessionNote extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (self $note): void {
            if (empty($note->qr_code_token)) {
                return;
            }

            $exists = static::query()
                ->when($note->exists, fn ($q) => $q->whereKeyNot($note->getKey()))
                ->where('qr_code_token', $note->qr_code_token)
                ->exists();

            if ($exists) {
                do {
                    $token = Str::random(32);
                } while (static::query()->where('qr_code_token', $token)->exists());

                $note->qr_code_token = $token;
            }
        });
    }

    protected $casts = [
        'assessment_methods' => 'array',
        'intervention_ids' => 'array',
        'is_locked' => 'boolean',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function patientSession()
    {
        return $this->belongsTo(PatientSession::class, 'patient_session_id');
    }

    public function getSubjectiveAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        $parts = array_filter([
            $this->attributes['subjective_complaint'] ?? null,
            $this->attributes['subjective_problem'] ?? null,
        ]);

        return !empty($parts) ? implode("\n\n", $parts) : null;
    }
}
