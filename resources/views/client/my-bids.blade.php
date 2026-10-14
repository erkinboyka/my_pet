@extends('layouts.app')

@section('title', 'Мои ставки')

@section('content')
<h1>Мои ставки</h1>

@php
    $winsCount = \App\Models\Auction::where('winner_id', auth()->id())->count();
@endphp
<p class="page-sub">Всего ваших ставок: {{ $bids->total() }} · <a href="{{ route('client.wins') }}">Победы ({{ $winsCount }})</a></p>

<table class="table">
    <thead>
        <tr>
            <th>Лот</th>
            <th>Моя ставка</th>
            <th>Текущая цена лота</th>
            <th>Статус лота</th>
            <th>Время</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($bids as $bid)
            @php
                $lot = $bid->auction;
                $status = $lot?->displayStatus();
                $isLeader = $lot && (float) $lot->current_price === (float) $bid->amount && $status === 'active';
            @endphp
            <tr>
                <td>
                    @if ($lot)
                        <a href="{{ route('auctions.show', $lot) }}">{{ $lot->title }}</a>
                    @else
                        —
                    @endif
                </td>
                <td><strong>{{ number_format((float) $bid->amount, 0, ',', ' ') }} сомони</strong></td>
                <td>{{ $lot ? number_format($lot->currentPrice(), 0, ',', ' ') : '—' }}</td>
                <td>
                    @if ($lot)
                        <span class="badge badge--{{ $status }}">{{ \App\Models\Auction::STATUSES[$status] ?? $status }}</span>
                        @if ($isLeader)
                            <span class="badge badge--active">лидер</span>
                        @endif
                    @endif
                </td>
                <td>{{ $bid->created_at->format('d.m.Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="empty">
                    Вы ещё не сделали ни одной ставки. <a href="{{ route('auctions.index') }}">Смотреть лоты →</a>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $bids->links('partials.pagination') }}
</div>
@endsection
