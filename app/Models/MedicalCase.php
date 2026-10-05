<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalCase extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'cases';

    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sessions()
    {
        return $this->hasMany(PatientSession::class, 'case_id');
    }
}

