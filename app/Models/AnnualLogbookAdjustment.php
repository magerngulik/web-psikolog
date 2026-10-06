<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualLogbookAdjustment extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'adjustment_count' => 'integer',
    ];
}

