<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoom extends Model
{
    protected $fillable = [
        'name',
        'is_direct',
        'created_by',
    ];

    protected $casts = [
        'is_direct' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_room_users')
                    ->withPivot('last_read_at')
                    ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->latest();
    }

    public function unreadCount(User $user): int
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;
        if (!$pivot || !$pivot->last_read_at) {
            return $this->messages()->count();
        }
        return $this->messages()->where('created_at', '>', $pivot->last_read_at)->count();
    }
}
