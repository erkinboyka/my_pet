@extends('layouts.app')

@section('title', $auction->title)

@php
    $status = $auction->displayStatus();
@endphp

@section('content')
<p><a href="{{ route('auctions.index') }}">← К списку лотов</a></p>

<div class="lot-page">
    <div class="lot-page__main">

        <div class="lot-header">
            <span class="badge badge--{{ $status }}">{{ \App\Models\Auction::STATUSES[$status] ?? $status }}</span>
            <h1>{{ $auction->title }}</h1>
            @if ($auction->category)
                <p class="lot-header__category">Категория: {{ $auction->category->name }}</p>
            @endif
        </div>

        <div class="lot-info card">
            <div class="lot-info__row">
                <span>Открытие:</span>
                <strong>{{ $auction->start_at->format('d.m.Y H:i') }}</strong>
            </div>
            <div class="lot-info__row">
                <span>Закрытие:</span>
                <strong>{{ $auction->end_at->format('d.m.Y H:i') }}</strong>
            </div>
            <div class="lot-info__row">
                <span>Минимальный шаг:</span>
                <strong>{{ number_format((float) $auction->minimum_bid_step, 0, ',', ' ') }} сомони</strong>
            </div>
            @if ($auction->moderator)
                <div class="lot-info__row">
                    <span>Модератор лота:</span>
                    <strong>{{ $auction->moderator->name }}</strong>
                </div>
            @endif
        </div>

        <section class="section">
            <h2>Описание</h2>
            <p class="lot-description">{{ $auction->description }}</p>
        </section>

        <section class="section">
            <h2>История ставок</h2>

            @if ($auction->bids->isEmpty())
                <p class="empty">Ставок пока нет. Будьте первым!</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Участник</th>
                            <th>Сумма</th>
                            <th>Время</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($auction->bids as $bid)
                            <tr>
                                <td>{{ $bid->user?->shortName() ?? 'Удалённый пользователь' }}</td>
                                <td><strong>{{ number_format((float) $bid->amount, 0, ',', ' ') }} сомони</strong></td>
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
                <p class="price-box__label">Аукцион завершён</p>
                <p class="price-box__value">{{ number_format($auction->currentPrice(), 0, ',', ' ') }} сомони</p>

                @if ($auction->winner_id)
                    <p class="price-box__label">Победитель</p>
                    <p class="price-box__winner">{{ $auction->winner?->shortName() }}</p>
                @else
                    <p class="price-box__empty">Победитель отсутствует</p>
                @endif

            @elseif ($status === 'scheduled')
                <p class="price-box__label">Стартовая цена</p>
                <p class="price-box__value">{{ number_format($auction->currentPrice(), 0, ',', ' ') }} сомони</p>
                <p class="price-box__note">Откроется: {{ $auction->start_at->format('d.m.Y H:i') }}</p>
                <button class="btn btn--block" disabled>Ставки пока недоступны</button>

            @elseif ($status === 'active')
                <p class="price-box__label">
                    @if ($auction->current_price !== null)
                        Текущая ставка
                    @else
                        Стартовая цена
                    @endif
                </p>
                <p class="price-box__value">{{ number_format($auction->currentPrice(), 0, ',', ' ') }} сомони</p>

                <p class="price-box__label">Минимальная следующая ставка</p>
                <p class="price-box__min">{{ number_format($auction->nextMinBid(), 0, ',', ' ') }} сомони</p>

                <p class="price-box__label">До окончания</p>
                <p class="price-box__timer countdown" data-end="{{ $auction->end_at->toIso8601String() }}">--:--:--</p>

                @guest
                    <a href="{{ route('login') }}" class="btn btn--primary btn--block">Войдите, чтобы сделать ставку</a>
                @else
                    @if (auth()->user()->isClient())
                        @if ($auction->moderator_id === auth()->id() || $auction->created_by === auth()->id())
                            <p class="price-box__note">Это ваш лот — ставить на него нельзя.</p>
                        @else
                            <form method="POST" action="{{ route('bids.store', $auction) }}" class="bid-form" id="bid-form">
                                @csrf

                                <label for="bid-amount">Ваша ставка (сомони)</label>
                                <input type="number" id="bid-amount" name="amount"
                                       value="{{ old('amount', $auction->nextMinBid()) }}"
                                       min="1" step="1" required>

                                @error('amount')
                                    <span class="form__error">{{ $message }}</span>
                                @enderror

                                <button type="submit" class="btn btn--primary btn--block" id="bid-submit">
                                    Сделать ставку
                                </button>
                            </form>
                        @endif
                    @else
                        <p class="price-box__note">Ставки доступны только участникам (роль client).</p>
                    @endif
                @endguest

            @elseif ($status === 'cancelled' || $status === 'rejected')
                <p class="price-box__label">Лот закрыт</p>
                <p class="price-box__empty">Аукцион не состоялся.</p>

            @else
                <p class="price-box__label">Стартовая цена</p>
                <p class="price-box__value">{{ number_format($auction->currentPrice(), 0, ',', ' ') }} сомони</p>
            @endif
        </div>
    </aside>
</div>
@endsection
