@extends('layouts.app')

@section('title', 'Редактирование сотрудника')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Редактирование сотрудника: {{ $user->display_name }}</h2>
    
    <form method="POST" action="{{ route('admin.employees.update', $user) }}" style="margin-top: 1.5rem;">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" minlength="8">
            <small style="color: #666;">Оставьте пустым, чтобы не менять пароль. Минимум 8 символов.</small>
        </div>
        
        <div class="form-group">
            <label for="full_name">ФИО</label>
            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                   placeholder="Иванов Иван Иванович">
        </div>
        
        <div class="form-group">
            <label for="position">Должность</label>
            <input type="text" id="position" name="position" value="{{ old('position', $user->position) }}"
                   placeholder="Менеджер">
        </div>
        
        <div class="form-group">
            <label for="description">Описание</label>
            <textarea id="description" name="description" rows="3" 
                      placeholder="Краткая информация о сотруднике...">{{ old('description', $user->description) }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="status">Статус</label>
            <select id="status" name="status">
                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Активен</option>
                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Не активен</option>
                <option value="vacation" {{ old('status', $user->status) === 'vacation' ? 'selected' : '' }}>В отпуске</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="work_schedule">График работы</label>
            <input type="text" id="work_schedule" name="work_schedule" value="{{ old('work_schedule', $user->work_schedule) }}"
                   placeholder="9:00-18:00">
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_admin" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                Права администратора
            </label>
        </div>
        
        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
            <button type="submit" class="btn">Сохранить</button>
            <a href="{{ route('admin.employees') }}" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>
@endsection
