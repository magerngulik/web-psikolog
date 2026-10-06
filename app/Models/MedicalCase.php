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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!empty($model->subjective_complaint) && empty($model->complaint)) {
                $model->complaint = $model->subjective_complaint;
            } elseif (!empty($model->complaint) && empty($model->subjective_complaint)) {
                $model->subjective_complaint = $model->complaint;
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sessions()
    {
        return $this->hasMany(PatientSession::class, 'case_id');
    }
}

