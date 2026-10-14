<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Онлайн-аукцион')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<header class="header">
    <div class="container header__inner">
        <a href="{{ route('home') }}" class="logo">Online<span>Auction</span></a>

        <nav class="nav">
            <a href="{{ route('auctions.index') }}">Лоты</a>

            @auth
                <span class="nav__user">{{ auth()->user()->name }}</span>

                @if (auth()->user()->isClient())
                    <a href="{{ route('client.my-bids') }}">Мои ставки</a>
                    <a href="{{ route('client.wins') }}">Победы</a>
                @endif

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">Админ</a>
                @elseif (auth()->user()->isModerator())
                    <a href="{{ route('moderator.dashboard') }}">Кабинет</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn--ghost btn--sm">Выйти</button>
                </form>
            @else
                <a href="{{ route('login') }}">Войти</a>
                <a href="{{ route('register') }}" class="btn btn--primary btn--sm">Регистрация</a>
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
        <p>Online Auction &copy; {{ date('Y') }} — учебный проект.</p>
        <p><a href="{{ route('home') }}">Главная</a></p>
    </div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
