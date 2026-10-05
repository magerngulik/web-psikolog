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
}

