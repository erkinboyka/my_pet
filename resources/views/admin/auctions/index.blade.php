@extends('layouts.admin')

@section('title', 'Лоты')

@section('content')
<div class="page-head">
    <h1>Лоты</h1>
    <a href="{{ route('admin.auctions.create') }}" class="btn btn--primary btn--sm">+ Создать лот</a>
</div>

<form method="GET" action="{{ route('admin.auctions.index') }}" class="filters card">
    <div class="filters__row">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Название лота">
        <select name="status">
            <option value="">Все статусы</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn--primary btn--sm">Найти</button>
        <a href="{{ route('admin.auctions.index') }}" class="btn btn--ghost btn--sm">Сбросить</a>
    </div>
</form>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Статус</th>
            <th>Цена</th>
            <th>Открытие</th>
            <th>Закрытие</th>
            <th>Модератор</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($lots as $lot)
            @php $status = $lot->displayStatus(); @endphp
            <tr>
                <td>{{ $lot->id }}</td>
                <td><a href="{{ route('auctions.show', $lot) }}">{{ $lot->title }}</a></td>
                <td><span class="badge badge--{{ $status }}">{{ $statuses[$status] ?? $status }}</span></td>
                <td>{{ number_format($lot->currentPrice(), 0, ',', ' ') }}</td>
                <td>{{ $lot->start_at->format('d.m.Y H:i') }}</td>
                <td>{{ $lot->end_at->format('d.m.Y H:i') }}</td>
                <td>{{ $lot->moderator?->name ?? '—' }}</td>
                <td class="table__actions">
                    @if ($lot->status === 'pending_moderation')
                        <form method="POST" action="{{ route('admin.auctions.approve', $lot) }}" class="inline-form">
                            @csrf
                            <button type="submit" class="btn btn--primary btn--sm">Одобрить</button>
                        </form>

                        <form method="POST" action="{{ route('admin.auctions.reject', $lot) }}" class="inline-form">
                            @csrf
                            <button type="submit" class="btn btn--danger btn--sm">Отклонить</button>
                        </form>
                    @elseif (! in_array($lot->status, ['finished', 'cancelled']))
                        <a href="{{ route('admin.auctions.edit', $lot) }}" class="btn btn--ghost btn--sm">Изменить</a>

                        <form method="POST" action="{{ route('admin.auctions.cancel', $lot) }}" class="inline-form"
                              onsubmit="return confirm('Отменить лот {{ $lot->title }}?')">
                            @csrf
                            <button type="submit" class="btn btn--danger btn--sm">Отменить</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="empty">Лоты не найдены.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $lots->links('partials.pagination') }}
</div>
@endsection
