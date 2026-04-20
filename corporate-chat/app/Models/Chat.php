<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_group',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_group' => 'boolean',
        ];
    }

    /**
     * Пользователи в чате
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_user')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    /**
     * Сообщения в чате
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Создатель чата
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Получить название чата
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }

        if (!$this->is_group && $this->users()->count() === 2) {
            // Для личного чата вернуть имя другого пользователя
            $otherUser = $this->users->firstWhere('id', '!=', auth()->id());
            if ($otherUser) {
                return $otherUser->display_name;
            }
        }

        return 'Чат #' . $this->id;
    }

    /**
     * Проверка, является ли пользователь участником чата
     */
    public function isParticipant(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Добавить пользователя в чат
     */
    public function addParticipant(User $user): void
    {
        if (!$this->isParticipant($user)) {
            $this->users()->attach($user);
        }
    }

    /**
     * Удалить пользователя из чата
     */
    public function removeParticipant(User $user): void
    {
        $this->users()->detach($user);
    }
}
