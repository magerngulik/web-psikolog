<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalHoliday extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $holiday): void {
            if (! empty($holiday->holiday_date)) {
                static::query()
                    ->whereDate('holiday_date', $holiday->holiday_date)
                    ->delete();
            }
        });
    }
}


