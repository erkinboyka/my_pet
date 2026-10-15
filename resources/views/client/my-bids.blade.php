@extends('layouts.app')

@section('title', __('client.myBidsTitle'))

@section('content')
<h1>{{ __('client.myBidsTitle') }}</h1>

@php
    $winsCount = \App\Models\Auction::where('winner_id', auth()->id())->count();
@endphp
<p class="page-sub">
    {{ __('client.totalBids', ['count' => $bids->total()]) }} ·
    <a href="{{ route('client.wins') }}">{{ __('client.winsLink', ['count' => $winsCount]) }}</a>
</p>

<table class="table">
    <thead>
        <tr>
            <th>{{ __('nav.lots') }}</th>
            <th>{{ __('client.yourBid') }}</th>
            <th>{{ __('client.lotPrice') }}</th>
            <th>{{ __('client.lotStatus') }}</th>
            <th>{{ __('auction.time') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($bids as $bid)
            @php
                $lot = $bid->auction;
                $status = $lot?->displayStatus();
                $isLeader = $lot && (float) $lot->current_price === (float) $bid->amount && $status === 'active';
            @endphp
            <tr>
                <td>
                    @if ($lot)
                        <a href="{{ route('auctions.show', $lot) }}">{{ $lot->title }}</a>
                    @else
                        —
                    @endif
                </td>
                <td><strong>{{ format_price((float) $bid->amount) }}</strong></td>
                <td>{{ $lot ? format_price($lot->currentPrice()) : '—' }}</td>
                <td>
                    @if ($lot)
                        <span class="badge badge--{{ $status }}">{{ __('status.'.$status) }}</span>
                        @if ($isLeader)
                            <span class="badge badge--active">{{ __('client.leader') }}</span>
                        @endif
                    @endif
                </td>
                <td>{{ $bid->created_at->format('d.m.Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="empty">
                    {{ __('client.noBids') }}
                    <a href="{{ route('auctions.index') }}">{{ __('client.watchLots') }}</a>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $bids->links('partials.pagination') }}
</div>
@endsection
