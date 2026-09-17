<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'contact_id',
        'campaign_id',
        'whatsapp_message_id',
        'direction',
        'type',
        'body',
        'media_payload',
        'status',
        'error_details',
    ];

    protected $casts = [
        'media_payload' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(MessageStatusLog::class);
    }
}
