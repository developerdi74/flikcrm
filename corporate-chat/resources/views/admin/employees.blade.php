@extends('layouts.app')

@section('title', 'Сотрудники')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h2>Сотрудники</h2>
    <a href="{{ route('admin.employees.create') }}" class="btn">Добавить сотрудника</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ФИО</th>
                <th>Email</th>
                <th>Должность</th>
                <th>Статус</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->full_name ?? 'Не указано' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->position ?? '—' }}</td>
                    <td>
                        @if($user->status === 'active')
                            <span class="badge badge-success">Активен</span>
                        @elseif($user->status === 'inactive')
                            <span class="badge badge-danger">Не активен</span>
                        @else
                            <span class="badge badge-warning">В отпуске</span>
                        @endif
                    </td>
                    <td>
                        @if($user->isAdmin())
                            <span class="badge badge-info">Администратор</span>
                        @else
                            Сотрудник
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.employees.edit', $user) }}" class="btn btn-secondary" 
                           style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">✏️</a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.employees.destroy', $user) }}" method="POST" 
                                  style="display: inline;" 
                                  onsubmit="return confirm('Вы уверены, что хотите удалить этого сотрудника?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">🗑️</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: #666;">
                        Список сотрудников пуст
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($users->hasPages())
        <div style="margin-top: 1rem;">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
