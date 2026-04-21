<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoneyGiftLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'user_id',
        'previous_amount',
        'new_amount',
        'note',
    ];

    protected $casts = [
        'previous_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

