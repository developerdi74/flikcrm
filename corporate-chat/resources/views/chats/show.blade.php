@extends('layouts.app')

@section('title', $chat->name ?? 'Чат')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2>
                @if($chat->is_group)
                    {{ $chat->name ?? 'Групповой чат #' . $chat->id }}
                    
                    <button onclick="document.getElementById('edit-name-form').style.display='block'" 
                            class="btn btn-secondary" 
                            style="padding: 0.25rem 0.75rem; font-size: 0.875rem; margin-left: 0.5rem;">
                        ✏️
                    </button>
                    
                    <form id="edit-name-form" method="POST" action="{{ route('chats.update-name', $chat) }}" 
                          style="display: none; margin-top: 0.5rem;">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $chat->name }}" 
                               style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" required>
                        <button type="submit" class="btn" style="padding: 0.5rem 1rem;">Сохранить</button>
                        <button type="button" onclick="this.form.parentElement.style.display='none'" 
                                class="btn btn-secondary" style="padding: 0.5rem 1rem;">Отмена</button>
                    </form>
                @else
                    Личный чат
                @endif
            </h2>
            <p style="color: #666; font-size: 0.875rem;">
                Участников: {{ $chat->users->count() }}
                @if($chat->is_group)
                    <span class="badge badge-info">Групповой</span>
                @else
                    <span class="badge badge-success">Личный</span>
                @endif
            </p>
        </div>
        <a href="{{ route('chats.index') }}" class="btn btn-secondary">← Назад к чатам</a>
    </div>
    
    @if($chat->is_group && $chat->users->count() > 0)
        <div style="margin-bottom: 1rem; padding: 0.75rem; background: #f8f9fa; border-radius: 4px;">
            <strong>Участники:</strong>
            <span style="color: #666;">
                {{ $chat->users->pluck('display_name')->join(', ') }}
            </span>
        </div>
    @endif
    
    <div class="chat-messages" id="chat-messages">
        @forelse($chat->messages as $message)
            <div class="message {{ $message->user_id === auth()->id() ? 'own' : 'other' }}">
                <div class="message-header">
                    <strong>{{ $message->user->display_name }}</strong>
                    <span>{{ $message->created_at->format('H:i d.m.Y') }}</span>
                </div>
                <div class="message-body">{{ $message->message }}</div>
            </div>
        @empty
            <p style="text-align: center; color: #666; padding: 2rem;">
                В этом чате пока нет сообщений. Будьте первым!
            </p>
        @endforelse
    </div>
    
    <form method="POST" action="{{ route('chats.message', $chat) }}" style="margin-top: 1rem;">
        @csrf
        <div style="display: flex; gap: 0.5rem;">
            <textarea name="message" rows="3" 
                      style="flex: 1; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"
                      placeholder="Введите сообщение..." required></textarea>
            <button type="submit" class="btn">Отправить</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Автопрокрутка вниз при загрузке
    const messagesContainer = document.getElementById('chat-messages');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Подключение к WebSocket для получения новых сообщений в реальном времени
    // Для работы необходимо запустить Laravel Reverb: php artisan reverb:start
</script>
@endpush
@endsection
