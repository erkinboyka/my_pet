@extends('layouts.app')

@section('title', $auction->title)

@php
    $status = $auction->displayStatus();
@endphp

@section('content')
<p><a href="{{ route('auctions.index') }}">{{ __('auction.backToList') }}</a></p>

<div class="lot-page">
    <div class="lot-page__main">

        <div class="lot-header">
            <span class="badge badge--{{ $status }}">{{ __('status.'.$status) }}</span>
            <h1>{{ $auction->title }}</h1>
            @if ($auction->category)
                <p class="lot-header__category">{{ __('auction.category') }} {{ $auction->category->name }}</p>
            @endif
        </div>

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
                <span>{{ __('auction.step') }}</span>
                <strong>{{ format_price((float) $auction->minimum_bid_step) }}</strong>
            </div>
            @if ($auction->moderator)
                <div class="lot-info__row">
                    <span>{{ __('auction.moderatorRow') }}</span>
                    <strong>{{ $auction->moderator->name }}</strong>
                </div>
            @endif
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
                            <th>{{ __('auction.participant') }}</th>
                            <th>{{ __('auction.amount') }}</th>
                            <th>{{ __('auction.time') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($auction->bids as $bid)
                            <tr>
                                <td>{{ $bid->user ? $bid->user->shortName() : __('auction.deletedUser') }}</td>
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
            @if ($status === 'finished')
                <p class="price-box__label">{{ __('auction.finishedTitle') }}</p>
                <p class="price-box__value">{{ format_price($auction->currentPrice()) }}</p>

                @if ($auction->winner_id)
                    <p class="price-box__label">{{ __('auction.winner') }}</p>
                    <p class="price-box__winner">{{ $auction->winner ? $auction->winner->shortName() : '' }}</p>
                @else
                    <p class="price-box__empty">{{ __('auction.noWinner') }}</p>
                @endif

            @elseif ($status === 'scheduled')
                <p class="price-box__label">{{ __('auction.startPrice') }}</p>
                <p class="price-box__value">{{ format_price($auction->currentPrice()) }}</p>
                <p class="price-box__note">{{ __('auction.openNote', ['when' => $auction->start_at->format('d.m.Y H:i')]) }}</p>
                <button class="btn btn--block" disabled>{{ __('auction.bidUnavailable') }}</button>

            @elseif ($status === 'active')
                <p class="price-box__label">
                    {{ $auction->current_price !== null ? __('auction.current') : __('auction.starting') }}
                </p>
                <p class="price-box__value">{{ format_price($auction->currentPrice()) }}</p>

                <p class="price-box__label">{{ __('auction.minNext') }}</p>
                <p class="price-box__min">{{ format_price($auction->nextMinBid()) }}</p>

                <p class="price-box__label">{{ __('auction.untilEnd') }}</p>
                <p class="price-box__timer countdown" data-end="{{ $auction->end_at->toIso8601String() }}">--:--:--</p>

                @guest
                    <a href="{{ route('login') }}" class="btn btn--primary btn--block">{{ __('auction.loginToBid') }}</a>
                @else
                    @if (auth()->user()->isClient())
                        @if ($auction->moderator_id === auth()->id() || $auction->created_by === auth()->id())
                            <p class="price-box__note">{{ __('auction.ownerNote') }}</p>
                        @else
                            <form method="POST" action="{{ route('bids.store', $auction) }}" class="bid-form" id="bid-form">
                                @csrf

                                <label for="bid-amount">{{ __('bid.label') }}</label>
                                <input type="number" id="bid-amount" name="amount"
                                       value="{{ old('amount', $auction->nextMinBid()) }}"
                                       min="1" step="1" required>

                                @error('amount')
                                    <span class="form__error">{{ $message }}</span>
                                @enderror

                                <button type="submit" class="btn btn--primary btn--block" id="bid-submit"
                                        data-loading-text="{{ __('bid.submitting') }}">
                                    {{ __('bid.submit') }}
                                </button>
                            </form>
                        @endif
                    @else
                        <p class="price-box__note">{{ __('auction.clientOnly') }}</p>
                    @endif
                @endguest

            @elseif ($status === 'cancelled' || $status === 'rejected')
                <p class="price-box__label">{{ __('auction.closedTitle') }}</p>
                <p class="price-box__empty">{{ __('auction.closedText') }}</p>

            @else
                <p class="price-box__label">{{ __('auction.startPrice') }}</p>
                <p class="price-box__value">{{ format_price($auction->currentPrice()) }}</p>
            @endif
        </div>
    </aside>
</div>
@endsection
