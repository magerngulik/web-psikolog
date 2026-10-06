<?php

namespace App\Models;

class Appointment extends PatientSession
{
    protected $table = 'patient_sessions';

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
