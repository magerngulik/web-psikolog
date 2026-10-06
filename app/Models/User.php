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
        return $value ?: '503/123-SIPA/2026';
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
}
