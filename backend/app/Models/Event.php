<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_km',
        'event_date',
        'description',
        'description_km',
        'sort_order',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];
}
