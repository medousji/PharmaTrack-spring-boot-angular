<?php
// app/Models/ChatbotConversation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotConversation extends Model
{
    protected $fillable = [
        'user_id',
        'question',
        'reponse',
        'intention',
        'donnees',
    ];

    protected $casts = [
        'donnees' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}