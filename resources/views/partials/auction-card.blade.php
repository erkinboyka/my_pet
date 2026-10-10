@php
    $status = $auction->displayStatus();
@endphp

<div class="card lot-card">
    <div class="lot-card__image">{{ mb_strtoupper(mb_substr($auction->title, 0, 1)) }}</div>

    <div class="lot-card__body">
        <span class="badge badge--{{ $status }}">{{ \App\Models\Auction::STATUSES[$status] ?? $status }}</span>

        <h3 class="lot-card__title">
            <a href="{{ route('auctions.show', $auction) }}">{{ $auction->title }}</a>
        </h3>

        @if ($auction->category)
            <p class="lot-card__category">{{ $auction->category->name }}</p>
        @endif

        <p class="lot-card__price">
            @if ($auction->current_price !== null)
                Текущая ставка:
                <strong>{{ number_format($auction->currentPrice(), 0, ',', ' ') }} сомони</strong>
            @else
                Стартовая цена:
                <strong>{{ number_format($auction->currentPrice(), 0, ',', ' ') }} сомони</strong>
            @endif
        </p>

        <p class="lot-card__time">
            @if ($status === 'active')
                До окончания:
                <span class="countdown" data-end="{{ $auction->end_at->toIso8601String() }}">--:--:--</span>
            @elseif ($status === 'scheduled')
                Откроется: {{ $auction->start_at->format('d.m.Y H:i') }}
            @elseif ($status === 'finished')
                Завершён {{ $auction->end_at->format('d.m.Y H:i') }}
            @endif
        </p>

        <a href="{{ route('auctions.show', $auction) }}" class="btn btn--primary btn--sm">Подробнее</a>
    </div>
</div>
