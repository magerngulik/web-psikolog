<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSequentialCode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use GeneratesSequentialCode, HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'fee' => 'decimal:2',
        'skp_points' => 'decimal:2',
    ];

    public function sequentialCodeColumn(): string
    {
        return 'activity_code';
    }

    public function sequentialCodePrefix(): string
    {
        return 'ACT-'.date('Ym').'-';
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

