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

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_date' => 'date',
        'birth_order' => 'integer',
        'total_siblings' => 'integer',
        'is_disabled' => 'boolean',
    ];

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

    public function getEducationAttribute()
    {
        return $this->attributes['last_education'] ?? null;
    }

    public function getSiblingInfoAttribute()
    {
        $order = $this->attributes['birth_order'] ?? null;
        $total = $this->attributes['total_siblings'] ?? null;

        if ($order && $total) {
            return "Anak ke-{$order} dari {$total} bersaudara";
        } elseif ($order) {
            return "Anak ke-{$order}";
        }

        return '-';
    }

    public function getDisabledStatusAttribute()
    {
        if (empty($this->attributes['is_disabled'])) {
            return 'Tidak';
        }

        $desc = trim($this->attributes['disability_description'] ?? '');
        return $desc ? "Ya ({$desc})" : 'Ya (Difabel)';
    }
}

