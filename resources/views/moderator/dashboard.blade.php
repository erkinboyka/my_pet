@extends('layouts.moderator')

@section('title', 'Обзор')

@section('content')
<h1>Мой обзор</h1>

<div class="stats stats--4">
    <div class="stat card">
        <span class="stat__value">{{ $stats['total'] }}</span>
        <span class="stat__label">Моих лотов</span>
    </div>
    <div class="stat card">
        <span class="stat__value">{{ $stats['live'] }}</span>
        <span class="stat__label">Идут сейчас</span>
    </div>
    <div class="stat card">
        <span class="stat__value">{{ $stats['pending'] }}</span>
        <span class="stat__label">На модерации</span>
    </div>
    <div class="stat card">
        <span class="stat__value">{{ $stats['finished'] }}</span>
        <span class="stat__label">Завершено</span>
    </div>
</div>

<section class="section">
    <h2>Идут сейчас</h2>

    @if ($liveLots->isEmpty())
        <p class="empty">Активных лотов нет.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Лот</th>
                    <th>Текущая цена</th>
                    <th>Закрытие</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($liveLots as $lot)
                    <tr>
                        <td>{{ $lot->title }}</td>
                        <td><strong>{{ number_format($lot->currentPrice(), 0, ',', ' ') }} сомони</strong></td>
                        <td>
                            <span class="countdown" data-end="{{ $lot->end_at->toIso8601String() }}">--:--:--</span>
                        </td>
                        <td>
                            <a href="{{ route('moderator.auctions.show', $lot) }}" class="btn btn--ghost btn--sm">Открыть</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>

<section class="section">
    <h2>Последние ставки на моих лотах</h2>

    @if ($recentBids->isEmpty())
        <p class="empty">Ставок пока нет.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Участник</th>
                    <th>Лот</th>
                    <th>Сумма</th>
                    <th>Время</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentBids as $bid)
                    <tr>
                        <td>{{ $bid->user?->name ?? '—' }}</td>
                        <td>{{ $bid->auction?->title ?? '—' }}</td>
                        <td><strong>{{ number_format((float) $bid->amount, 0, ',', ' ') }}</strong></td>
                        <td>{{ $bid->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
@endsection
