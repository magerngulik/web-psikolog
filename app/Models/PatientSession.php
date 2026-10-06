<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientSession extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'patient_sessions';

    protected $guarded = [];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_high_risk' => 'boolean',
        'generates_report' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!empty($model->medical_case_id) && empty($model->case_id)) {
                $model->case_id = $model->medical_case_id;
            } elseif (!empty($model->case_id) && empty($model->medical_case_id)) {
                $model->medical_case_id = $model->case_id;
            }
        });
    }

    public function medicalCase()
    {
        return $this->belongsTo(MedicalCase::class, 'case_id');
    }

    public function patient()
    {
        return $this->hasOneThrough(
            Client::class,
            MedicalCase::class,
            'id', // Foreign key on cases table...
            'id', // Foreign key on clients table...
            'case_id', // Local key on patient_sessions table...
            'client_id' // Local key on cases table...
        );
    }

    public function psychologist()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(function () {
            return User::first() ?? new User([
                'name' => 'Psikolog Klinis',
                'sipa_number' => '503/123-SIPA/2026',
            ]);
        });
    }

    public function sessionNote()
    {
        return $this->hasOne(SessionNote::class, 'appointment_id');
    }

    public function getDateAttribute()
    {
        return $this->attributes['session_date'] ?? null;
    }
}

