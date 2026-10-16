@extends('layouts.app')

@section('title', __('errors.403title'))

@section('content')
<div class="error-page">
    <h1>403</h1>
    <p class="error-page__text">{{ __('errors.403text') }}</p>
    <div class="error-page__actions">
        <a href="{{ route('home') }}" class="btn btn--primary">{{ __('errors.home') }}</a>
        <a href="{{ route('auctions.index') }}" class="btn btn--ghost">{{ __('nav.lots') }}</a>
    </div>
</div>
@endsection
