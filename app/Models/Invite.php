<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invite extends Model
{
    protected $fillable = [
        'event_id',
        'token',
        'table_number',
        'is_used',
        'is_reserved',
        'invitee_name',
        'invitee_phone',
        'has_plus_one',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'is_reserved' => 'boolean',
        'has_plus_one' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
