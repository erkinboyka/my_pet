@extends('layouts.admin')

@section('title', __('admin.moderators.title'))

@section('content')
<h1>{{ __('admin.moderators.title') }}</h1>

@forelse ($moderators as $moderator)
    <div class="card moderator-card">
        <div class="moderator-card__head">
            <div>
                <h3>{{ $moderator->name }}</h3>
                <p class="moderator-card__email">{{ $moderator->email }}</p>
            </div>
            <span class="badge badge--draft">
                {{ __('admin.moderators.lotsCount', ['count' => $moderator->managed_auctions_count]) }}
            </span>
        </div>

        @if ($moderator->managedAuctions->isEmpty())
            <p class="empty">{{ __('admin.moderators.noLots') }}</p>
        @else
            <ul class="moderator-card__lots">
                @foreach ($moderator->managedAuctions as $lot)
                    @php $status = $lot->displayStatus(); @endphp
                    <li>
                        <span class="badge badge--{{ $status }}">{{ __('status.'.$status) }}</span>
                        <a href="{{ route('auctions.show', $lot) }}">#{{ $lot->id }} {{ $lot->title }}</a>
                        <span class="moderator-card__price">
                            {{ format_price($lot->currentPrice()) }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@empty
    <p class="empty">{{ __('admin.moderators.empty') }}</p>
@endforelse
@endsection
