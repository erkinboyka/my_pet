@extends('layouts.moderator')

@section('title', __('mod.dashboard.title'))

@section('content')
<h1>{{ __('mod.dashboard.title') }}</h1>

<div class="stats stats--4">
    <div class="stat card">
        <span class="stat__value">{{ $stats['total'] }}</span>
        <span class="stat__label">{{ __('mod.stat.total') }}</span>
    </div>
    <div class="stat card">
        <span class="stat__value">{{ $stats['live'] }}</span>
        <span class="stat__label">{{ __('admin.stat.live') }}</span>
    </div>
    <div class="stat card">
        <span class="stat__value">{{ $stats['pending'] }}</span>
        <span class="stat__label">{{ __('admin.stat.pending') }}</span>
    </div>
    <div class="stat card">
        <span class="stat__value">{{ $stats['finished'] }}</span>
        <span class="stat__label">{{ __('admin.stat.finished') }}</span>
    </div>
</div>

<section class="section">
    <h2>{{ __('admin.stat.live') }}</h2>

    @if ($liveLots->isEmpty())
        <p class="empty">{{ __('mod.noLive') }}</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('admin.col.lot') }}</th>
                    <th>{{ __('auction.current') }}</th>
                    <th>{{ __('admin.col.close') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($liveLots as $lot)
                    <tr>
                        <td>{{ $lot->title }}</td>
                        <td><strong>{{ format_price($lot->currentPrice()) }}</strong></td>
                        <td>
                            <span class="countdown" data-end="{{ $lot->end_at->toIso8601String() }}">--:--:--</span>
                        </td>
                        <td>
                            <a href="{{ route('moderator.auctions.show', $lot) }}" class="btn btn--ghost btn--sm">{{ __('mod.open') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>

<section class="section">
    <h2>{{ __('mod.recentBids') }}</h2>

    @if ($recentBids->isEmpty())
        <p class="empty">{{ __('admin.noBids') }}</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('admin.col.user') }}</th>
                    <th>{{ __('admin.col.lot') }}</th>
                    <th>{{ __('auction.amount') }}</th>
                    <th>{{ __('admin.col.time') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentBids as $bid)
                    <tr>
                        <td>{{ $bid->user ? $bid->user->name : '—' }}</td>
                        <td>{{ $bid->auction ? $bid->auction->title : '—' }}</td>
                        <td><strong>{{ format_price((float) $bid->amount) }}</strong></td>
                        <td>{{ $bid->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
@endsection
