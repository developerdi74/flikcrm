@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>Мой профиль</h2>
        <a href="{{ route('profile.edit') }}" class="btn">Редактировать</a>
    </div>
    
    <table style="width: 100%;">
        <tr>
            <th style="width: 200px;">ФИО</th>
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
            <th>Статус</th>
            <td>
                @if($user->status === 'active')
                    <span class="badge badge-success">Активен</span>
                @elseif($user->status === 'inactive')
                    <span class="badge badge-danger">Не активен</span>
                @else
                    <span class="badge badge-warning">В отпуске</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>График работы</th>
            <td>{{ $user->work_schedule ?? 'Не указан' }}</td>
        </tr>
        <tr>
            <th>Описание</th>
            <td>{{ $user->description ?? 'Нет описания' }}</td>
        </tr>
        <tr>
            <th>Роль</th>
            <td>
                @if($user->isAdmin())
                    <span class="badge badge-info">Администратор</span>
                @else
                    <span class="badge badge-secondary" style="background: #6c757d; color: white;">Сотрудник</span>
                @endif
            </td>
        </tr>
    </table>
</div>
@endsection
