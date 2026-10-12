@extends('layouts.admin')

@section('title', 'Пользователи')

@section('content')
<div class="page-head">
    <h1>Пользователи</h1>

    <form method="GET" action="{{ route('admin.users.index') }}" class="page-head__filters">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Имя или email">
        <select name="role">
            <option value="">Все роли</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>admin</option>
            <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>moderator</option>
            <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>client</option>
        </select>
        <button type="submit" class="btn btn--primary btn--sm">Найти</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Роль</th>
            <th>Статус</th>
            <th>Дата регистрации</th>
            <th>Действия</th>
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
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>admin</option>
                                <option value="moderator" {{ $user->role === 'moderator' ? 'selected' : '' }}>moderator</option>
                                <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>client</option>
                            </select>
                        </form>
                    @endif
                </td>
                <td>
                    @if ($user->is_active)
                        <span class="badge badge--active">активен</span>
                    @else
                        <span class="badge badge--cancelled">заблокирован</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                <td class="table__actions">
                    @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.block', $user) }}" class="inline-form">
                            @csrf
                            <button type="submit" class="btn btn--ghost btn--sm">
                                {{ $user->is_active ? 'Заблокировать' : 'Разблокировать' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-form"
                              onsubmit="return confirm('Удалить пользователя {{ $user->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--danger btn--sm">Удалить</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="empty">Пользователи не найдены.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $users->links('partials.pagination') }}
</div>
@endsection
