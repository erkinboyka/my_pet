@extends('layouts.app')

@section('title', __('home.title'))

@section('content')

<section class="hero">
    <h1>{{ __('home.title') }}</h1>
    <p>{{ __('home.text') }}</p>
    <div class="hero__actions">
        <a href="{{ route('auctions.index') }}" class="btn btn--primary">{{ __('home.viewLots') }}</a>
        @guest
            <a href="{{ route('register') }}" class="btn btn--ghost">{{ __('home.register') }}</a>
        @endguest
    </div>
</section>

@if ($activeLots->isNotEmpty())
<section class="section">
    <h2>{{ __('home.activeLots') }}</h2>
    <div class="lots-grid">
        @foreach ($activeLots as $lot)
            @include('partials.auction-card', ['auction' => $lot])
        @endforeach
    </div>
    <p class="section__more"><a href="{{ route('auctions.index') }}">{{ __('home.allLots') }}</a></p>
</section>
@endif

<section class="section">
    <h2>{{ __('home.how') }}</h2>
    <div class="steps">
        <div class="step">
            <div class="step__num">1</div>
            <h3>{{ __('home.step1t') }}</h3>
            <p>{{ __('home.step1d') }}</p>
        </div>
        <div class="step">
            <div class="step__num">2</div>
            <h3>{{ __('home.step2t') }}</h3>
            <p>{{ __('home.step2d') }}</p>
        </div>
        <div class="step">
            <div class="step__num">3</div>
            <h3>{{ __('home.step3t') }}</h3>
            <p>{{ __('home.step3d') }}</p>
        </div>
    </div>
</section>

@if ($upcomingLots->isNotEmpty())
<section class="section">
    <h2>{{ __('home.upcoming') }}</h2>
    <div class="lots-grid">
        @foreach ($upcomingLots as $lot)
            @include('partials.auction-card', ['auction' => $lot])
        @endforeach
    </div>
</section>
@endif

@if ($finishedLots->isNotEmpty())
<section class="section">
    <h2>{{ __('home.finished') }}</h2>
    <div class="lots-grid">
        @foreach ($finishedLots as $lot)
            @include('partials.auction-card', ['auction' => $lot])
        @endforeach
    </div>
</section>
@endif

<section class="section rules">
    <h2>{{ __('home.rules') }}</h2>
    <ul>
        <li>{{ __('home.rule1') }}</li>
        <li>{{ __('home.rule2') }}</li>
        <li>{{ __('home.rule3') }}</li>
        <li>{{ __('home.rule4') }}</li>
    </ul>
</section>

@endsection
