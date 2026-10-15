@extends('layouts.admin')

@section('title', __('admin.users.title'))

@section('content')
<div class="page-head">
    <h1>{{ __('admin.users.title') }}</h1>

    <form method="GET" action="{{ route('admin.users.index') }}" class="page-head__filters">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.users.search') }}">
        <select name="role">
            <option value="">{{ __('admin.users.allRoles') }}</option>
            @foreach (['admin', 'moderator', 'client'] as $roleKey)
                <option value="{{ $roleKey }}" {{ request('role') === $roleKey ? 'selected' : '' }}>{{ $roleKey }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn--primary btn--sm">{{ __('admin.users.find') }}</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>{{ __('admin.col.id') }}</th>
            <th>{{ __('admin.col.name') }}</th>
            <th>{{ __('admin.col.email') }}</th>
            <th>{{ __('admin.col.role') }}</th>
            <th>{{ __('admin.col.status') }}</th>
            <th>{{ __('admin.col.registered') }}</th>
            <th>{{ __('admin.col.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
            <tr class="{{ $user->is_active ? '' : 'table__row--inactive' }}">
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if ($user->id === auth()->id())
                        <span class="table__plain">{{ $user->role }}</span>
                    @else
                        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline-form">
                            @csrf
                            <select name="role" class="table__select" onchange="this.form.submit()">
                                @foreach (['admin', 'moderator', 'client'] as $roleKey)
                                    <option value="{{ $roleKey }}" {{ $user->role === $roleKey ? 'selected' : '' }}>{{ $roleKey }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </td>
                <td>
                    @if ($user->is_active)
                        <span class="badge badge--active">{{ __('admin.users.active') }}</span>
                    @else
                        <span class="badge badge--cancelled">{{ __('admin.users.blocked') }}</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                <td class="table__actions">
                    @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.block', $user) }}" class="inline-form">
                            @csrf
                            <button type="submit" class="btn btn--ghost btn--sm">
                                {{ $user->is_active ? __('admin.users.block') : __('admin.users.unblock') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-form"
                              onsubmit="return confirm('{{ __('admin.users.deleteConfirm', ['name' => $user->name]) }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--danger btn--sm">{{ __('admin.users.delete') }}</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="empty">{{ __('admin.users.none') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $users->links('partials.pagination') }}
</div>
@endsection
