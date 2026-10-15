@extends('layouts.app')

@section('title', __('client.winsTitle'))

@section('content')
<h1>{{ __('client.winsTitle') }}</h1>

<p class="page-sub">
    {{ __('client.winsSub') }} ·
    <a href="{{ route('client.my-bids') }}">{{ __('client.bidsLink') }}</a>
</p>

<table class="table">
    <thead>
        <tr>
            <th>{{ __('nav.lots') }}</th>
            <th>{{ __('client.category') }}</th>
            <th>{{ __('client.winningSum') }}</th>
            <th>{{ __('client.finishedAt') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($wins as $win)
            <tr>
                <td><a href="{{ route('auctions.show', $win) }}">{{ $win->title }}</a></td>
                <td>{{ $win->category?->name ?? '—' }}</td>
                <td><strong>{{ format_price((float) ($win->winning_price ?? $win->currentPrice())) }}</strong></td>
                <td>{{ $win->end_at->format('d.m.Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="empty">{{ __('client.noWins') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $wins->links('partials.pagination') }}
</div>
@endsection
