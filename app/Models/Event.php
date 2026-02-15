<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'address',
        'rsvp_enabled',
        'event_time',
        'admin_password_hash',
    ];

    protected $casts = [
        'rsvp_enabled' => 'boolean',
        'event_time' => 'datetime',
    ];

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }
}
