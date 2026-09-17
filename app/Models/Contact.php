<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'group_tag',
        'custom_attributes',
    ];

    protected $casts = [
        'custom_attributes' => 'array',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function broadcastQueues(): HasMany
    {
        return $this->hasMany(BroadcastQueue::class);
    }
}
