@extends('layouts.app')

@section('title', 'Вход в систему')

@section('content')
<div class="card" style="max-width: 400px; margin: 2rem auto;">
    <h2 style="margin-bottom: 1.5rem; text-align: center;">Вход в систему</h2>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="remember"> Запомнить меня
            </label>
        </div>
        
        <button type="submit" class="btn" style="width: 100%;">Войти</button>
    </form>
</div>
@endsection
