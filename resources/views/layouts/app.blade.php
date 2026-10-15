<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Online Auction')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<header class="header">
    <div class="container header__inner">
        <a href="{{ route('home') }}" class="logo">Online<span>Auction</span></a>

        <nav class="nav">
            <div class="switcher">
                <a href="{{ route('lang.set', 'ru') }}"
                   class="switcher__item {{ app()->getLocale() === 'ru' ? 'switcher__item--active' : '' }}">RU</a>
                <a href="{{ route('lang.set', 'en') }}"
                   class="switcher__item {{ app()->getLocale() === 'en' ? 'switcher__item--active' : '' }}">EN</a>
            </div>

            <div class="switcher">
                <a href="{{ route('currency.set', 'TJS') }}"
                   class="switcher__item {{ current_currency() === 'TJS' ? 'switcher__item--active' : '' }}">TJS</a>
                <a href="{{ route('currency.set', 'USD') }}"
                   class="switcher__item {{ current_currency() === 'USD' ? 'switcher__item--active' : '' }}">USD</a>
            </div>

            <a href="{{ route('auctions.index') }}">{{ __('nav.lots') }}</a>

            @auth
                <span class="nav__user">{{ auth()->user()->name }}</span>

                @if (auth()->user()->isClient())
                    <a href="{{ route('client.my-bids') }}">{{ __('nav.myBids') }}</a>
                    <a href="{{ route('client.wins') }}">{{ __('nav.wins') }}</a>
                @endif

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">{{ __('nav.admin') }}</a>
                @elseif (auth()->user()->isModerator())
                    <a href="{{ route('moderator.dashboard') }}">{{ __('nav.cabinet') }}</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn--ghost btn--sm">{{ __('nav.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}">{{ __('nav.login') }}</a>
                <a href="{{ route('register') }}" class="btn btn--primary btn--sm">{{ __('nav.register') }}</a>
            @endauth
        </nav>
    </div>
</header>

<main class="main">
    <div class="container">

        @if (session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert--error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<footer class="footer">
    <div class="container">
        <p>{{ __('footer.text', ['year' => date('Y')]) }}</p>
        <p><a href="{{ route('home') }}">{{ __('footer.home') }}</a></p>
    </div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
