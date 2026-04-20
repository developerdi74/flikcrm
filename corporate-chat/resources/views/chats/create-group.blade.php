@extends('layouts.app')

@section('title', 'Создание группового чата')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Создание группового чата</h2>
    
    <form method="POST" action="{{ route('chats.group.store') }}" style="margin-top: 1.5rem;">
        @csrf
        
        <div class="form-group">
            <label for="name">Название чата *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                   placeholder="Введите название группового чата">
        </div>
        
        <div class="form-group">
            <label>Участники *</label>
            <p style="color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">
                Выберите сотрудников, которых хотите добавить в чат
            </p>
            
            <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 0.75rem;">
                @foreach($users as $user)
                    <label style="display: flex; align-items: center; padding: 0.5rem; cursor: pointer; {{ $loop->last ? '' : 'border-bottom: 1px solid #eee;' }}">
                        <input type="checkbox" name="users[]" value="{{ $user->id }}" 
                               style="margin-right: 0.75rem;"
                               {{ in_array($user->id, old('users', [])) ? 'checked' : '' }}>
                        <span>
                            {{ $user->display_name }}
                            @if($user->position)
                                <span style="color: #666; font-size: 0.875rem;">— {{ $user->position }}</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
        
        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
            <button type="submit" class="btn">Создать чат</button>
            <a href="{{ route('chats.index') }}" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>
@endsection
