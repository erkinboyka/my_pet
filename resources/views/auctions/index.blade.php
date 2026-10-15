@extends('layouts.app')

@section('title', __('auctions.title'))

@section('content')
<h1>{{ __('auctions.title') }}</h1>

<form method="GET" action="{{ route('auctions.index') }}" class="filters card">
    <div class="filters__row">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('auctions.search') }}" class="filters__search">

        <select name="category">
            <option value="">{{ __('auctions.allCategories') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="sort">
            @foreach (['end_asc', 'end_desc', 'price_asc', 'price_desc', 'new'] as $sortKey)
                <option value="{{ $sortKey }}" {{ $sort === $sortKey ? 'selected' : '' }}>
                    {{ __('auctions.sort.'.$sortKey) }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn--primary">{{ __('auctions.find') }}</button>

        @if (request('q') || request('category'))
            <a href="{{ route('auctions.index') }}" class="btn btn--ghost">{{ __('auctions.reset') }}</a>
        @endif
    </div>

    <div class="filters__tabs">
        @foreach (['live' => 'tab.live', 'upcoming' => 'tab.upcoming', 'finished' => 'tab.finished', 'all' => 'tab.all'] as $tabKey => $tabLabel)
            <a href="{{ route('auctions.index', ['tab' => $tabKey, 'q' => request('q'), 'category' => request('category'), 'sort' => request('sort')]) }}"
               class="filters__tab {{ $tab === $tabKey ? 'filters__tab--active' : '' }}">{{ __($tabLabel) }}</a>
        @endforeach
    </div>
</form>

<div class="lots-grid">
    @forelse ($lots as $lot)
        @include('partials.auction-card', ['auction' => $lot])
    @empty
        <p class="empty">{{ __('auctions.notFound') }}</p>
    @endforelse
</div>

<div class="pagination">
    {{ $lots->links('partials.pagination') }}
</div>
@endsection
