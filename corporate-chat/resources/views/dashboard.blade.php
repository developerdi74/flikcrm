@extends('layouts.app')

@section('title', 'Дашборд')

@section('content')
<div class="card">
    <h2>Добро пожаловать, {{ $user->display_name }}!</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
        <div class="card" style="background-color: #3498db; color: white;">
            <h3>Чаты</h3>
            <p style="font-size: 2.5rem; font-weight: bold;">{{ $chatsCount }}</p>
        </div>
        
        <div class="card" style="background-color: #2ecc71; color: white;">
            <h3>Сообщения</h3>
            <p style="font-size: 2.5rem; font-weight: bold;">{{ $messagesCount }}</p>
        </div>
        
        <div class="card" style="background-color: #9b59b6; color: white;">
            <h3>Статус</h3>
            <p style="font-size: 1.5rem; margin-top: 0.5rem;">
                @if($user->status === 'active')
                    <span class="badge badge-success">Активен</span>
                @elseif($user->status === 'inactive')
                    <span class="badge badge-danger">Не активен</span>
                @else
                    <span class="badge badge-warning">В отпуске</span>
                @endif
            </p>
        </div>
    </div>
    
    <div style="margin-top: 1.5rem;">
        <a href="{{ route('chats.index') }}" class="btn">Перейти к чатам</a>
        <a href="{{ route('profile.edit') }}" class="btn btn-secondary">Редактировать профиль</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.employees.create') }}" class="btn">Добавить сотрудника</a>
        @endif
    </div>
</div>

<div class="card">
    <h3>Информация о профиле</h3>
    <table style="margin-top: 1rem;">
        <tr>
            <th>ФИО</th>
            <td>{{ $user->full_name ?? 'Не указано' }}</td>
        </tr>
        <tr>
            <th>Должность</th>
            <td>{{ $user->position ?? 'Не указана' }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>График работы</th>
            <td>{{ $user->work_schedule ?? 'Не указан' }}</td>
        </tr>
        <tr>
            <th>Описание</th>
            <td>{{ $user->description ?? 'Нет описания' }}</td>
        </tr>
    </table>
</div>
@endsection
