@extends('layouts.moderator')

@section('title', __('mod.myLots'))

@section('content')
<div class="page-head">
    <h1>{{ __('mod.myLots') }}</h1>
    <a href="{{ route('moderator.auctions.create') }}" class="btn btn--primary btn--sm">{{ __('mod.newLot') }}</a>
</div>

<form method="GET" action="{{ route('moderator.auctions.index') }}" class="filters card">
    <div class="filters__row">
        <select name="status">
            <option value="">{{ __('admin.auctions.allStatuses') }}</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ __('status.'.$key) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn--primary btn--sm">{{ __('admin.auctions.show') }}</button>
        <a href="{{ route('moderator.auctions.index') }}" class="btn btn--ghost btn--sm">{{ __('auctions.reset') }}</a>
    </div>
</form>

<table class="table">
    <thead>
        <tr>
            <th>{{ __('admin.col.id') }}</th>
            <th>{{ __('admin.col.name') }}</th>
            <th>{{ __('admin.col.status') }}</th>
            <th>{{ __('admin.col.price') }}</th>
            <th>{{ __('admin.col.open') }}</th>
            <th>{{ __('admin.col.close') }}</th>
            <th>{{ __('admin.col.actions') }}</th>
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
                <td><span class="badge badge--{{ $status }}">{{ __('status.'.$status) }}</span></td>
                <td>{{ format_price($lot->currentPrice()) }}</td>
                <td>{{ $lot->start_at->format('d.m.Y H:i') }}</td>
                <td>{{ $lot->end_at->format('d.m.Y H:i') }}</td>
                <td class="table__actions">
                    @can('update', $lot)
                        <a href="{{ route('moderator.auctions.edit', $lot) }}" class="btn btn--ghost btn--sm">{{ __('admin.auctions.edit') }}</a>
                    @endcan

                    @can('cancel', $lot)
                        <form method="POST" action="{{ route('moderator.auctions.cancel', $lot) }}" class="inline-form"
                              onsubmit="return confirm('{{ __('admin.auctions.cancelConfirm', ['title' => $lot->title]) }}')">
                            @csrf
                            <button type="submit" class="btn btn--danger btn--sm">{{ __('admin.auctions.cancel') }}</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="empty">
                    {{ __('mod.none') }}
                    <a href="{{ route('moderator.auctions.create') }}">{{ __('mod.createLink') }}</a>.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $lots->links('partials.pagination') }}
</div>
@endsection
