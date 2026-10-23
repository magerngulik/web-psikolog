<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSequentialCode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use GeneratesSequentialCode, HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function sequentialCodeColumn(): string
    {
        return 'org_code';
    }

    public function sequentialCodePrefix(): string
    {
        return 'ORG-'.date('Ym').'-';
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

