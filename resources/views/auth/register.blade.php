@extends('layouts.app')

@section('title', __('auth.registerTitle'))

@section('content')
<div class="auth-box">
    <h1>{{ __('auth.registerTitle') }}</h1>

    <form method="POST" action="{{ route('register.store') }}" class="form">
        @csrf

        <div class="form__group">
            <label for="name">{{ __('auth.name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form__group">
            <label for="email">{{ __('auth.email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form__group">
            <label for="phone">{{ __('auth.phone') }} <span class="form__optional">{{ __('auth.optional') }}</span></label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+992 90 000 00 00">
            @error('phone')
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

        <div class="form__group">
            <label for="password_confirmation">{{ __('auth.passwordConfirm') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div class="form__group form__group--check">
            <label>
                <input type="checkbox" name="agree" value="1" {{ old('agree') ? 'checked' : '' }}>
                {{ __('auth.agree') }}
            </label>
            @error('agree')
                <span class="form__error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn--primary btn--block">{{ __('auth.submitRegister') }}</button>

        <p class="auth-box__hint">
            {{ __('auth.haveAccount') }} <a href="{{ route('login') }}">{{ __('auth.loginLink') }}</a>
        </p>
    </form>
</div>
@endsection
