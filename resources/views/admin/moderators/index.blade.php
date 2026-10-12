@extends('layouts.admin')

@section('title', 'Модераторы')

@section('content')
<h1>Модераторы</h1>

@forelse ($moderators as $moderator)
    <div class="card moderator-card">
        <div class="moderator-card__head">
            <div>
                <h3>{{ $moderator->name }}</h3>
                <p class="moderator-card__email">{{ $moderator->email }}</p>
            </div>
            <span class="badge badge--draft">
                лотов: {{ $moderator->managed_auctions_count }}
            </span>
        </div>

        @if ($moderator->managedAuctions->isEmpty())
            <p class="empty">Назначенных лотов нет.</p>
        @else
            <ul class="moderator-card__lots">
                @foreach ($moderator->managedAuctions as $lot)
                    @php $status = $lot->displayStatus(); @endphp
                    <li>
                        <span class="badge badge--{{ $status }}">{{ $statuses[$status] ?? $status }}</span>
                        <a href="{{ route('auctions.show', $lot) }}">#{{ $lot->id }} {{ $lot->title }}</a>
                        <span class="moderator-card__price">
                            {{ number_format($lot->currentPrice(), 0, ',', ' ') }} сомони
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@empty
    <p class="empty">Модераторов пока нет. Назначьте роль moderator на странице пользователей.</p>
@endforelse
@endsection
