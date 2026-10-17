<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'sipa_number',
        'title_prefix',
        'title_suffix',
        'practice_name',
        'practice_city',
        'practice_address',
        'phone',
        'signature_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getSipaNumberAttribute($value)
    {
        return $value ?: '503/123-SIPPK/2026';
    }

    public function getSippkNumberAttribute()
    {
        return $this->sipa_number;
    }

    public function getPracticeNameAttribute($value)
    {
        return $value ?: 'MANDIRI';
    }

    public function getPracticeCityAttribute($value)
    {
        return $value ?: 'Selatpanjang';
    }

    public function getFormattedNameAttribute(): string
    {
        $prefix = $this->title_prefix ? trim($this->title_prefix) . ' ' : '';
        $suffix = $this->title_suffix ? ', ' . trim($this->title_suffix) : '';
        return $prefix . ($this->name ?: 'Psikolog Klinis') . $suffix;
    }

    public function getSignatureBase64Attribute(): ?string
    {
        if (empty($this->signature_path)) {
            return null;
        }

        try {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($disk->exists($this->signature_path)) {
                $content = $disk->get($this->signature_path);
                $mime = $disk->mimeType($this->signature_path) ?: 'image/png';
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Error generating signature base64: ' . $e->getMessage());
        }

        return null;
    }

    public function getSignatureUrlAttribute(): ?string
    {
        if (empty($this->signature_path)) {
            return null;
        }

        try {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->signature_path);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
