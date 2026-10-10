@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div class="auth-box">
    <h1>Регистрация</h1>

    <form method="POST" action="{{ route('register.store') }}" class="form">
        @csrf

        <div class="form__group">
            <label for="name">Имя</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form__group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form__group">
            <label for="phone">Телефон <span class="form__optional">(необязательно)</span></label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+992 90 000 00 00">
            @error('phone')
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

        <div class="form__group">
            <label for="password_confirmation">Повторите пароль</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div class="form__group form__group--check">
            <label>
                <input type="checkbox" name="agree" value="1" {{ old('agree') ? 'checked' : '' }}>
                Я согласен с правилами аукциона
            </label>
            @error('agree')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn--primary btn--block">Создать аккаунт</button>

        <p class="auth-box__hint">
            Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
        </p>
    </form>
</div>
@endsection
