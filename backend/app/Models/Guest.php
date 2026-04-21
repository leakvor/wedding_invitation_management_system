<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'invitation_code',
        'status',
        'rsvp_status',
        'allowed_guests',
        'money',
        'checked_in_at',
        'qr_token',
        'invitation_slug',
        'notes',
    ];

    protected $casts = [
        'money' => 'decimal:2',
        'checked_in_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Guest $guest): void {
            if (!$guest->invitation_slug) {
                $guest->invitation_slug = (string) Str::uuid();
            }

            if (!$guest->qr_token) {
                $guest->qr_token = (string) Str::uuid();
            }
        });
    }

    public function moneyLogs(): HasMany
    {
        return $this->hasMany(MoneyGiftLog::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(GuestComment::class)->latest();
    }

    public function ensureInvitationAccess(): void
    {
        $updates = [];

        if (!$this->invitation_slug) {
            $updates['invitation_slug'] = (string) Str::uuid();
        }

        if (!$this->qr_token) {
            $updates['qr_token'] = (string) Str::uuid();
        }

        if ($updates !== []) {
            $this->forceFill($updates)->save();
        }
    }
}
