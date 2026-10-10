@extends('layouts.app')

@section('title', 'Лоты')

@section('content')
<h1>Активные лоты</h1>

<div class="lots-grid">
    @forelse ($lots as $lot)
        @include('partials.auction-card', ['auction' => $lot])
    @empty
        <p class="empty">Сейчас нет активных лотов.</p>
    @endforelse
</div>
@endsection
