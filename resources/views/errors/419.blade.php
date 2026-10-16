@extends('layouts.app')

@section('title', '419')

@section('content')
<div class="error-page">
    <h1>419</h1>
    <p class="error-page__text">Сессия устарела. Обновите страницу и попробуйте ещё раз.</p>
    <div class="error-page__actions">
        <a href="{{ route('home') }}" class="btn btn--primary">{{ __('errors.home') }}</a>
    </div>
</div>
@endsection
