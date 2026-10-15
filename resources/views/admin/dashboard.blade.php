@extends('layouts.admin')

@section('title', __('admin.dashboard.title'))

@section('content')
<h1>{{ __('admin.dashboard.title') }}</h1>

<div class="stats">
    <div class="stat card">
        <span class="stat__value">{{ $stats['users'] }}</span>
        <span class="stat__label">{{ __('admin.stat.users') }}</span>
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
    <div class="stat card">
        <span class="stat__value">{{ $stats['total'] }}</span>
        <span class="stat__label">{{ __('admin.stat.total') }}</span>
    </div>
</div>

<section class="section">
    <h2>{{ __('admin.recentBids') }}</h2>

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
                        <td>{{ $bid->user?->name ?? '—' }}</td>
                        <td>
                            @if ($bid->auction)
                                <a href="{{ route('auctions.show', $bid->auction) }}">{{ $bid->auction->title }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td><strong>{{ format_price((float) $bid->amount) }}</strong></td>
                        <td>{{ $bid->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
@endsection
