@extends('layouts.admin')

@section('title', 'Обзор')

@section('content')
<h1>Обзор</h1>

<div class="stats">
    <div class="stat card">
        <span class="stat__value">{{ $stats['users'] }}</span>
        <span class="stat__label">Пользователей</span>
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
    <div class="stat card">
        <span class="stat__value">{{ $stats['total'] }}</span>
        <span class="stat__label">Всего лотов</span>
    </div>
</div>

<section class="section">
    <h2>Последние ставки</h2>

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
                        <td>
                            @if ($bid->auction)
                                <a href="{{ route('auctions.show', $bid->auction) }}">{{ $bid->auction->title }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td><strong>{{ number_format((float) $bid->amount, 0, ',', ' ') }}</strong></td>
                        <td>{{ $bid->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
@endsection
