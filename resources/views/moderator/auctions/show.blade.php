@extends('layouts.moderator')

@section('title', $auction->title)

@php
    $status = $auction->displayStatus();
@endphp

@section('content')
<p><a href="{{ route('moderator.auctions.index') }}">{{ __('admin.form.back') }}</a></p>

<div class="page-head">
    <div>
        <span class="badge badge--{{ $status }}">{{ __('status.'.$status) }}</span>
        <h1>{{ $auction->title }}</h1>
        @if ($auction->category)
            <p class="lot-header__category">{{ __('auction.category') }} {{ $auction->category->name }}</p>
        @endif
    </div>

    <div class="page-head__actions">
        @can('update', $auction)
            <a href="{{ route('moderator.auctions.edit', $auction) }}" class="btn btn--ghost btn--sm">{{ __('admin.auctions.edit') }}</a>
        @endcan

        @can('cancel', $auction)
            <form method="POST" action="{{ route('moderator.auctions.cancel', $auction) }}" class="inline-form"
                  onsubmit="return confirm('{{ __('admin.auctions.cancelConfirm', ['title' => $auction->title]) }}')">
                @csrf
                <button type="submit" class="btn btn--danger btn--sm">{{ __('admin.auctions.cancel') }}</button>
            </form>
        @endcan
    </div>
</div>

<div class="lot-page">
    <div class="lot-page__main">

        <div class="lot-info card">
            <div class="lot-info__row">
                <span>{{ __('auction.openAt') }}</span>
                <strong>{{ $auction->start_at->format('d.m.Y H:i') }}</strong>
            </div>
            <div class="lot-info__row">
                <span>{{ __('auction.closeAt') }}</span>
                <strong>{{ $auction->end_at->format('d.m.Y H:i') }}</strong>
            </div>
            <div class="lot-info__row">
                <span>{{ __('auction.startPrice') }}</span>
                <strong>{{ format_price((float) $auction->starting_price) }}</strong>
            </div>
            <div class="lot-info__row">
                <span>{{ __('auction.step') }}</span>
                <strong>{{ format_price((float) $auction->minimum_bid_step) }}</strong>
            </div>
        </div>

        <section class="section">
            <h2>{{ __('auction.description') }}</h2>
            <p class="lot-description">{{ $auction->description }}</p>
        </section>

        <section class="section">
            <h2>{{ __('auction.history') }}</h2>

            @if ($auction->bids->isEmpty())
                <p class="empty">{{ __('auction.noBids') }}</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.col.name') }}</th>
                            <th>{{ __('admin.col.email') }}</th>
                            <th>{{ __('auction.amount') }}</th>
                            <th>{{ __('auction.time') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($auction->bids as $bid)
                            <tr>
                                <td>{{ $bid->user?->name ?? '—' }}</td>
                                <td>{{ $bid->user?->email ?? '—' }}</td>
                                <td><strong>{{ format_price((float) $bid->amount) }}</strong></td>
                                <td>{{ $bid->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>

    <aside class="lot-page__side">
        <div class="price-box card">
            <p class="price-box__label">
                {{ $auction->current_price !== null ? __('auction.current') : __('auction.starting') }}
            </p>
            <p class="price-box__value">{{ format_price($auction->currentPrice()) }}</p>

            <p class="price-box__label">{{ __('mod.show.bidsTotal') }}</p>
            <p class="price-box__value">{{ $auction->bids->count() }}</p>

            @if ($status === 'active')
                <p class="price-box__label">{{ __('auction.untilEnd') }}</p>
                <p class="price-box__timer countdown" data-end="{{ $auction->end_at->toIso8601String() }}">--:--:--</p>
            @elseif ($status === 'pending_moderation')
                <p class="price-box__note">{{ __('mod.show.pendingNote') }}</p>
            @endif
        </div>
    </aside>
</div>
@endsection
