<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Online Auction') — Online Auction</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-body">

<header class="topbar">
    <a href="{{ route('home') }}" class="logo">Online<span>Auction</span></a>
    <div class="topbar__right">
        <div class="switcher">
            <a href="{{ route('lang.set', 'ru') }}"
               class="switcher__item {{ app()->getLocale() === 'ru' ? 'switcher__item--active' : '' }}">RU</a>
            <a href="{{ route('lang.set', 'en') }}"
               class="switcher__item {{ app()->getLocale() === 'en' ? 'switcher__item--active' : '' }}">EN</a>
        </div>
        <span class="topbar__hint">{{ __('mod.top.role') }} · {{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}" class="inline-form">
            @csrf
            <button type="submit" class="btn btn--ghost btn--sm">{{ __('nav.logout') }}</button>
        </form>
    </div>
</header>

<div class="admin-layout container">

    <aside class="sidebar">
        <a href="{{ route('moderator.dashboard') }}"
           class="sidebar__link {{ request()->routeIs('moderator.dashboard') ? 'sidebar__link--active' : '' }}">
            {{ __('admin.nav.dashboard') }}
        </a>
        <a href="{{ route('moderator.auctions.index') }}"
           class="sidebar__link {{ request()->routeIs('moderator.auctions.*') && !request()->routeIs('moderator.auctions.create') ? 'sidebar__link--active' : '' }}">
            {{ __('mod.myLots') }}
        </a>
        <a href="{{ route('moderator.auctions.create') }}"
           class="sidebar__link {{ request()->routeIs('moderator.auctions.create') ? 'sidebar__link--active' : '' }}">
            {{ __('mod.newLot') }}
        </a>

        <div class="sidebar__divider"></div>

        <a href="{{ route('home') }}" class="sidebar__link">{{ __('admin.nav.site') }}</a>
    </aside>

    <main class="admin-content">
        @if (session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert--error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
