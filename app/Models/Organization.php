<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($org) {
            if (empty($org->org_code)) {
                $yearMonth = date('Ym');
                $prefix = 'ORG-' . $yearMonth . '-';

                $lastOrg = self::withTrashed()
                    ->where('org_code', 'like', $prefix . '%')
                    ->orderBy('org_code', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastOrg && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $lastOrg->org_code, $matches)) {
                    $nextNumber = ((int) $matches[1]) + 1;
                }

                do {
                    $code = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                    $exists = self::withTrashed()->where('org_code', $code)->exists();
                    if ($exists) {
                        $nextNumber++;
                    }
                } while ($exists);

                $org->org_code = $code;
            }
        });
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'organization_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'school' => 'Sekolah',
            'university' => 'Universitas / Kampus',
            'community' => 'Komunitas',
            'corporate' => 'Korporat / Swasta',
            'government' => 'Instansi Pemerintah',
            'ngo' => 'LSM / Yayasan',
            default => 'Umum / Lainnya',
        };
    }
}

