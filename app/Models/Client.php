<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function cases()
    {
        return $this->hasMany(MedicalCase::class, 'client_id');
    }

    public function getNameAttribute()
    {
        return $this->attributes['full_name'] ?? ($this->attributes['name'] ?? 'Klien');
    }

    public function getMedicalRecordNumberAttribute()
    {
        return $this->attributes['client_code'] ?? ($this->attributes['medical_record_number'] ?? '-');
    }

    public function getDobAttribute()
    {
        $dob = $this->attributes['date_of_birth'] ?? ($this->attributes['birth_date'] ?? null);
        return $dob ? Carbon::parse($dob)->format('Y-m-d') : '-';
    }

    public function getAgeAttribute()
    {
        $dob = $this->attributes['date_of_birth'] ?? ($this->attributes['birth_date'] ?? null);
        return $dob ? Carbon::parse($dob)->age : 0;
    }
}
