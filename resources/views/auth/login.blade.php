@extends('layouts.app')

@section('title', __('auth.loginTitle'))

@section('content')
<div class="auth-box">
    <h1>{{ __('auth.loginTitle') }}</h1>

    <form method="POST" action="{{ route('login.store') }}" class="form">
        @csrf

        <div class="form__group">
            <label for="email">{{ __('auth.email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form__group">
            <label for="password">{{ __('auth.password') }}</label>
            <input type="password" id="password" name="password" required>
            @error('password')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form__group form__group--check">
            <label>
                <input type="checkbox" name="remember" value="1"> {{ __('auth.remember') }}
            </label>
        </div>

        <button type="submit" class="btn btn--primary btn--block">{{ __('auth.submitLogin') }}</button>

        <p class="auth-box__hint">
            {{ __('auth.noAccount') }} <a href="{{ route('register') }}">{{ __('auth.registerLink') }}</a>
        </p>
    </form>
</div>
@endsection
