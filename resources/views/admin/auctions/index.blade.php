@extends('layouts.admin')

@section('title', __('admin.auctions.title'))

@section('content')
<div class="page-head">
    <h1>{{ __('admin.auctions.title') }}</h1>
    <a href="{{ route('admin.auctions.create') }}" class="btn btn--primary btn--sm">{{ __('admin.auctions.create') }}</a>
</div>

<form method="GET" action="{{ route('admin.auctions.index') }}" class="filters card">
    <div class="filters__row">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.auctions.search') }}">
        <select name="status">
            <option value="">{{ __('admin.auctions.allStatuses') }}</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ __('status.'.$key) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn--primary btn--sm">{{ __('admin.users.find') }}</button>
        <a href="{{ route('admin.auctions.index') }}" class="btn btn--ghost btn--sm">{{ __('auctions.reset') }}</a>
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
            <th>{{ __('admin.col.moderator') }}</th>
            <th>{{ __('admin.col.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($lots as $lot)
            @php $status = $lot->displayStatus(); @endphp
            <tr>
                <td>{{ $lot->id }}</td>
                <td><a href="{{ route('auctions.show', $lot) }}">{{ $lot->title }}</a></td>
                <td><span class="badge badge--{{ $status }}">{{ __('status.'.$status) }}</span></td>
                <td>{{ format_price($lot->currentPrice()) }}</td>
                <td>{{ $lot->start_at->format('d.m.Y H:i') }}</td>
                <td>{{ $lot->end_at->format('d.m.Y H:i') }}</td>
                <td>{{ $lot->moderator ? $lot->moderator->name : '—' }}</td>
                <td class="table__actions">
                    @if ($lot->status === 'pending_moderation')
                        <form method="POST" action="{{ route('admin.auctions.approve', $lot) }}" class="inline-form">
                            @csrf
                            <button type="submit" class="btn btn--primary btn--sm">{{ __('admin.auctions.approve') }}</button>
                        </form>

                        <form method="POST" action="{{ route('admin.auctions.reject', $lot) }}" class="inline-form">
                            @csrf
                            <button type="submit" class="btn btn--danger btn--sm">{{ __('admin.auctions.reject') }}</button>
                        </form>
                    @elseif (! in_array($lot->status, ['finished', 'cancelled']))
                        <a href="{{ route('admin.auctions.edit', $lot) }}" class="btn btn--ghost btn--sm">{{ __('admin.auctions.edit') }}</a>

                        <form method="POST" action="{{ route('admin.auctions.cancel', $lot) }}" class="inline-form"
                              onsubmit="return confirm('{{ __('admin.auctions.cancelConfirm', ['title' => $lot->title]) }}')">
                            @csrf
                            <button type="submit" class="btn btn--danger btn--sm">{{ __('admin.auctions.cancel') }}</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="empty">{{ __('admin.auctions.none') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $lots->links('partials.pagination') }}
</div>
@endsection
