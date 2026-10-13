@extends('layouts.moderator')

@section('title', 'Мои лоты')

@section('content')
<div class="page-head">
    <h1>Мои лоты</h1>
    <a href="{{ route('moderator.auctions.create') }}" class="btn btn--primary btn--sm">+ Новый лот</a>
</div>

<form method="GET" action="{{ route('moderator.auctions.index') }}" class="filters card">
    <div class="filters__row">
        <select name="status">
            <option value="">Все статусы</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn--primary btn--sm">Показать</button>
        <a href="{{ route('moderator.auctions.index') }}" class="btn btn--ghost btn--sm">Сбросить</a>
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
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($lots as $lot)
            @php $status = $lot->displayStatus(); @endphp
            <tr>
                <td>{{ $lot->id }}</td>
                <td>
                    <a href="{{ route('moderator.auctions.show', $lot) }}">{{ $lot->title }}</a>
                </td>
                <td><span class="badge badge--{{ $status }}">{{ $statuses[$status] ?? $status }}</span></td>
                <td>{{ number_format($lot->currentPrice(), 0, ',', ' ') }}</td>
                <td>{{ $lot->start_at->format('d.m.Y H:i') }}</td>
                <td>{{ $lot->end_at->format('d.m.Y H:i') }}</td>
                <td class="table__actions">
                    @can('update', $lot)
                        <a href="{{ route('moderator.auctions.edit', $lot) }}" class="btn btn--ghost btn--sm">Изменить</a>
                    @endcan

                    @can('cancel', $lot)
                        <form method="POST" action="{{ route('moderator.auctions.cancel', $lot) }}" class="inline-form"
                              onsubmit="return confirm('Отменить лот {{ $lot->title }}?')">
                            @csrf
                            <button type="submit" class="btn btn--danger btn--sm">Отменить</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="empty">У вас пока нет лотов. <a href="{{ route('moderator.auctions.create') }}">Создайте первый</a>.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $lots->links('partials.pagination') }}
</div>
@endsection
