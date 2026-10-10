@extends('layouts.app')

@section('title', 'Онлайн-аукцион')

@section('content')

<section class="hero">
    <h1>Онлайн-аукцион</h1>
    <p>Делайте ставки. Следите за торгами. Получайте лучшие предложения.</p>
    <div class="hero__actions">
        <a href="{{ route('auctions.index') }}" class="btn btn--primary">Смотреть лоты</a>
        @guest
            <a href="{{ route('register') }}" class="btn btn--ghost">Регистрация</a>
        @endguest
    </div>
</section>

@if ($activeLots->isNotEmpty())
<section class="section">
    <h2>Активные лоты</h2>
    <div class="lots-grid">
        @foreach ($activeLots as $lot)
            @include('partials.auction-card', ['auction' => $lot])
        @endforeach
    </div>
    <p class="section__more"><a href="{{ route('auctions.index') }}">Все лоты →</a></p>
</section>
@endif

<section class="section">
    <h2>Как работает аукцион</h2>
    <div class="steps">
        <div class="step">
            <div class="step__num">1</div>
            <h3>Найдите лот</h3>
            <p>Выберите интересующий лот из списка активных аукционов.</p>
        </div>
        <div class="step">
            <div class="step__num">2</div>
            <h3>Сделайте ставку</h3>
            <p>Укажите сумму не меньше минимальной допустимой ставки.</p>
        </div>
        <div class="step">
            <div class="step__num">3</div>
            <h3>Заберите победу</h3>
            <p>Если к концу аукциона ваша ставка останется максимальной — лот ваш.</p>
        </div>
    </div>
</section>

@if ($upcomingLots->isNotEmpty())
<section class="section">
    <h2>Скоро откроются</h2>
    <div class="lots-grid">
        @foreach ($upcomingLots as $lot)
            @include('partials.auction-card', ['auction' => $lot])
        @endforeach
    </div>
</section>
@endif

@if ($finishedLots->isNotEmpty())
<section class="section">
    <h2>Завершённые аукционы</h2>
    <div class="lots-grid">
        @foreach ($finishedLots as $lot)
            @include('partials.auction-card', ['auction' => $lot])
        @endforeach
    </div>
</section>
@endif

<section class="section rules">
    <h2>Правила аукциона</h2>
    <ul>
        <li>Ставки принимаются только в период между открытием и закрытием лота.</li>
        <li>Ставка не может быть меньше текущей цены плюс минимальный шаг.</li>
        <li>После завершения аукциона новые ставки не принимаются.</li>
        <li>Победителем становится участник с максимальной ставкой на момент окончания.</li>
    </ul>
</section>

@endsection
