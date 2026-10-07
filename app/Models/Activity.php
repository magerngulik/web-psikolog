<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'fee' => 'decimal:2',
        'skp_points' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            if (empty($activity->activity_code)) {
                $yearMonth = date('Ym');
                $prefix = 'ACT-' . $yearMonth . '-';

                $lastActivity = self::withTrashed()
                    ->where('activity_code', 'like', $prefix . '%')
                    ->orderBy('activity_code', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastActivity && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $lastActivity->activity_code, $matches)) {
                    $nextNumber = ((int) $matches[1]) + 1;
                }

                do {
                    $code = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                    $exists = self::withTrashed()->where('activity_code', $code)->exists();
                    if ($exists) {
                        $nextNumber++;
                    }
                } while ($exists);

                $activity->activity_code = $code;
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function attachments()
    {
        return $this->hasMany(ActivityAttachment::class, 'activity_id');
    }

    public function invitationLetters()
    {
        return $this->hasMany(ActivityAttachment::class, 'activity_id')->where('file_type', 'invitation_letter');
    }

    public function certificates()
    {
        return $this->hasMany(ActivityAttachment::class, 'activity_id')->where('file_type', 'certificate');
    }

    public function photos()
    {
        return $this->hasMany(ActivityAttachment::class, 'activity_id')->where('file_type', 'documentation_photo');
    }

    public function getEventTypeLabelAttribute(): string
    {
        return match ($this->event_type) {
            'seminar' => 'Seminar',
            'webinar' => 'Webinar',
            'workshop' => 'Workshop / Pelatihan',
            'psychoeducation' => 'Psikoedukasi',
            'talkshow' => 'Talkshow',
            'training' => 'Training & Development',
            default => 'Kegiatan Lainnya',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'keynote_speaker' => 'Narasumber / Pembicara Utama',
            'co_speaker' => 'Co-Speaker / Pemateri Pendamping',
            'facilitator' => 'Fasilitator',
            'moderator' => 'Moderator',
            'assessor' => 'Asesor / Penguji',
            default => 'Narasumber',
        };
    }

    public function getDeliveryModeLabelAttribute(): string
    {
        return match ($this->delivery_mode) {
            'offline' => 'Tatap Muka (Offline)',
            'online' => 'Virtual (Online)',
            'hybrid' => 'Hybrid',
            default => 'Offline',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas (Paid)',
            'unpaid' => 'Belum Dibayar (Unpaid)',
            'waived_pro_bono' => 'Pro Bono / Sukarela',
            default => 'Belum Dibayar',
        };
    }
}

