@extends('layouts.app')

@section('title', 'Лоты')

@section('content')
<h1>Лоты аукциона</h1>

<form method="GET" action="{{ route('auctions.index') }}" class="filters card">
    <div class="filters__row">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск по названию..." class="filters__search">

        <select name="category">
            <option value="">Все категории</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="sort">
            <option value="end_asc" {{ $sort === 'end_asc' ? 'selected' : '' }}>Сначала ранние окончания</option>
            <option value="end_desc" {{ $sort === 'end_desc' ? 'selected' : '' }}>Сначала поздние окончания</option>
            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Цена: по возрастанию</option>
            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Цена: по убыванию</option>
            <option value="new" {{ $sort === 'new' ? 'selected' : '' }}>Сначала новые</option>
        </select>

        <button type="submit" class="btn btn--primary">Найти</button>

        @if (request('q') || request('category'))
            <a href="{{ route('auctions.index') }}" class="btn btn--ghost">Сбросить</a>
        @endif
    </div>

    <div class="filters__tabs">
        <a href="{{ route('auctions.index', ['tab' => 'live', 'q' => request('q'), 'category' => request('category')]) }}"
           class="filters__tab {{ $tab === 'live' ? 'filters__tab--active' : '' }}">Активные</a>
        <a href="{{ route('auctions.index', ['tab' => 'upcoming', 'q' => request('q'), 'category' => request('category')]) }}"
           class="filters__tab {{ $tab === 'upcoming' ? 'filters__tab--active' : '' }}">Скоро откроются</a>
        <a href="{{ route('auctions.index', ['tab' => 'finished', 'q' => request('q'), 'category' => request('category')]) }}"
           class="filters__tab {{ $tab === 'finished' ? 'filters__tab--active' : '' }}">Завершённые</a>
        <a href="{{ route('auctions.index', ['tab' => 'all', 'q' => request('q'), 'category' => request('category')]) }}"
           class="filters__tab {{ $tab === 'all' ? 'filters__tab--active' : '' }}">Все</a>
    </div>
</form>

<div class="lots-grid">
    @forelse ($lots as $lot)
        @include('partials.auction-card', ['auction' => $lot])
    @empty
        <p class="empty">Ничего не найдено. Попробуйте изменить параметры поиска.</p>
    @endforelse
</div>

<div class="pagination">
    {{ $lots->links('partials.pagination') }}
</div>
@endsection
