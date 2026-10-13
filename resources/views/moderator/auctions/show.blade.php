@extends('layouts.moderator')

@section('title', $auction->title)

@php
    $status = $auction->displayStatus();
@endphp

@section('content')
<p><a href="{{ route('moderator.auctions.index') }}">← К моим лотам</a></p>

<div class="page-head">
    <div>
        <span class="badge badge--{{ $status }}">{{ \App\Models\Auction::STATUSES[$status] ?? $status }}</span>
        <h1>{{ $auction->title }}</h1>
        @if ($auction->category)
            <p class="lot-header__category">Категория: {{ $auction->category->name }}</p>
        @endif
    </div>

    <div class="page-head__actions">
        @can('update', $auction)
            <a href="{{ route('moderator.auctions.edit', $auction) }}" class="btn btn--ghost btn--sm">Изменить</a>
        @endcan

        @can('cancel', $auction)
            <form method="POST" action="{{ route('moderator.auctions.cancel', $auction) }}" class="inline-form"
                  onsubmit="return confirm('Отменить лот?')">
                @csrf
                <button type="submit" class="btn btn--danger btn--sm">Отменить</button>
            </form>
        @endcan
    </div>
</div>

<div class="lot-page">
    <div class="lot-page__main">

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
                <span>Стартовая цена:</span>
                <strong>{{ number_format((float) $auction->starting_price, 0, ',', ' ') }} сомони</strong>
            </div>
            <div class="lot-info__row">
                <span>Минимальный шаг:</span>
                <strong>{{ number_format((float) $auction->minimum_bid_step, 0, ',', ' ') }} сомони</strong>
            </div>
        </div>

        <section class="section">
            <h2>Описание</h2>
            <p class="lot-description">{{ $auction->description }}</p>
        </section>

        <section class="section">
            <h2>История ставок</h2>

            @if ($auction->bids->isEmpty())
                <p class="empty">Ставок пока нет.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Участник</th>
                            <th>Email</th>
                            <th>Сумма</th>
                            <th>Время</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($auction->bids as $bid)
                            <tr>
                                <td>{{ $bid->user?->name ?? '—' }}</td>
                                <td>{{ $bid->user?->email ?? '—' }}</td>
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
            <p class="price-box__label">
                @if ($auction->current_price !== null)
                    Текущая ставка
                @else
                    Стартовая цена
                @endif
            </p>
            <p class="price-box__value">{{ number_format($auction->currentPrice(), 0, ',', ' ') }} сомони</p>

            <p class="price-box__label">Ставок всего</p>
            <p class="price-box__value">{{ $auction->bids->count() }}</p>

            @if ($status === 'active')
                <p class="price-box__label">До окончания</p>
                <p class="price-box__timer countdown" data-end="{{ $auction->end_at->toIso8601String() }}">--:--:--</p>
            @elseif ($status === 'pending_moderation')
                <p class="price-box__note">Лот ждёт одобрения администратора.</p>
            @endif
        </div>
    </aside>
</div>
@endsection
