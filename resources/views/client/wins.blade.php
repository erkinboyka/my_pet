@extends('layouts.app')

@section('title', 'Мои победы')

@section('content')
<h1>Мои победы</h1>

<p class="page-sub">Лоты, где ваша ставка осталась максимальной · <a href="{{ route('client.my-bids') }}">Мои ставки</a></p>

<table class="table">
    <thead>
        <tr>
            <th>Лот</th>
            <th>Категория</th>
            <th>Выигранная сумма</th>
            <th>Завершён</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($wins as $win)
            <tr>
                <td><a href="{{ route('auctions.show', $win) }}">{{ $win->title }}</a></td>
                <td>{{ $win->category?->name ?? '—' }}</td>
                <td><strong>{{ number_format((float) ($win->winning_price ?? $win->currentPrice()), 0, ',', ' ') }} сомони</strong></td>
                <td>{{ $win->end_at->format('d.m.Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="empty">Пока нет побед. Удачи на торгах!</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $wins->links('partials.pagination') }}
</div>
@endsection
