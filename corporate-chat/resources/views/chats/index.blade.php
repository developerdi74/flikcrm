@extends('layouts.app')

@section('title', 'Список чатов')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h2>Мои чаты</h2>
    <div>
        <a href="{{ route('chats.group.create') }}" class="btn">Создать групповой чат</a>
    </div>
</div>

<div class="card">
    @if($chats->count() > 0)
        <ul class="chat-list" style="list-style: none; padding: 0;">
            @foreach($chats as $chat)
                <li class="chat-item">
                    <div style="flex: 1;">
                        <h4 style="margin-bottom: 0.5rem;">
                            <a href="{{ route('chats.show', $chat) }}" style="color: #333; text-decoration: none;">
                                {{ $chat->name ?? ($chat->is_group ? 'Групповой чат #' . $chat->id : 'Личный чат') }}
                            </a>
                        </h4>
                        <p style="color: #666; font-size: 0.875rem;">
                            @if($chat->messages->count() > 0)
                                Последнее: {{ Str::limit($chat->messages->first()->message, 50) }}
                                <span style="color: #999;">— {{ $chat->messages->first()->created_at->diffForHumans() }}</span>
                            @else
                                Нет сообщений
                            @endif
                        </p>
                        <p style="color: #999; font-size: 0.75rem; margin-top: 0.25rem;">
                            Участников: {{ $chat->users->count() }}
                            @if($chat->is_group)
                                <span class="badge badge-info">Групповой</span>
                            @else
                                <span class="badge badge-success">Личный</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('chats.show', $chat) }}" class="btn">Открыть</a>
                </li>
            @endforeach
        </ul>
        
        <div style="margin-top: 1rem;">
            {{ $chats->links() }}
        </div>
    @else
        <p style="text-align: center; color: #666; padding: 2rem;">
            У вас пока нет чатов. Создайте новый чат или начните общение с коллегой.
        </p>
    @endif
</div>

@if(!$chats->count())
<div class="card">
    <h3>Начать общение</h3>
    <p style="margin: 1rem 0;">Выберите сотрудника для начала личного чата:</p>
    
    @php
        $users = \App\Models\User::where('id', '!=', auth()->id())->get();
    @endphp
    
    @if($users->count() > 0)
        <div style="display: grid; gap: 0.5rem;">
            @foreach($users as $user)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f8f9fa; border-radius: 4px;">
                    <span>{{ $user->display_name }} 
                        @if($user->position)
                            <span style="color: #666; font-size: 0.875rem;">({{ $user->position }})</span>
                        @endif
                    </span>
                    <a href="{{ route('chats.personal', $user) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Написать</a>
                </div>
            @endforeach
        </div>
    @else
        <p style="color: #666;">Нет других сотрудников в системе.</p>
    @endif
</div>
@endif
@endsection
