@extends('layouts.app')

@section('title', 'Вход')

@section('content')
<div class="auth-box">
    <h1>Вход</h1>

    <form method="POST" action="{{ route('login.store') }}" class="form">
        @csrf

        <div class="form__group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form__group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
            @error('password')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form__group form__group--check">
            <label>
                <input type="checkbox" name="remember" value="1"> Запомнить меня
            </label>
        </div>

        <button type="submit" class="btn btn--primary btn--block">Войти</button>

        <p class="auth-box__hint">
            Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a>
        </p>
    </form>
</div>
@endsection
