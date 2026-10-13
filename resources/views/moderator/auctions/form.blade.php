@extends('layouts.moderator')

@section('title', $auction->exists ? 'Редактирование лота' : 'Новый лот')

@section('content')
<p><a href="{{ route('moderator.auctions.index') }}">← К моим лотам</a></p>

<h1>{{ $auction->exists ? 'Редактирование лота' : 'Новый лот' }}</h1>

@if ($errors->any())
    <div class="alert alert--error">
        Проверьте заполнение полей:
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ $auction->exists ? route('moderator.auctions.update', $auction) : route('moderator.auctions.store') }}"
      class="form form--wide">
    @csrf

    <div class="form__group">
        <label for="title">Название</label>
        <input type="text" id="title" name="title" value="{{ old('title', $auction->title) }}" required>
        @error('title') <span class="form__error">{{ $message }}</span> @enderror
    </div>

    <div class="form__group">
        <label for="description">Описание</label>
        <textarea id="description" name="description" rows="5" required>{{ old('description', $auction->description) }}</textarea>
        @error('description') <span class="form__error">{{ $message }}</span> @enderror
    </div>

    <div class="form__group">
        <label for="category_id">Категория</label>
        <select id="category_id" name="category_id">
            <option value="">— без категории —</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id', $auction->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form__row">
        <div class="form__group">
            <label for="starting_price">Стартовая цена (сомони)</label>
            <input type="number" id="starting_price" name="starting_price" min="1" step="1"
                   value="{{ old('starting_price', $auction->starting_price) }}" required>
            @error('starting_price') <span class="form__error">{{ $message }}</span> @enderror
        </div>

        <div class="form__group">
            <label for="minimum_bid_step">Минимальный шаг (сомони)</label>
            <input type="number" id="minimum_bid_step" name="minimum_bid_step" min="1" step="1"
                   value="{{ old('minimum_bid_step', $auction->minimum_bid_step ?? 100) }}" required>
            @error('minimum_bid_step') <span class="form__error">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="form__row">
        <div class="form__group">
            <label for="start_at">Открытие</label>
            <input type="datetime-local" id="start_at" name="start_at"
                   value="{{ old('start_at', $auction->start_at?->format('Y-m-d\TH:i')) }}" required>
            @error('start_at') <span class="form__error">{{ $message }}</span> @enderror
        </div>

        <div class="form__group">
            <label for="end_at">Закрытие</label>
            <input type="datetime-local" id="end_at" name="end_at"
                   value="{{ old('end_at', $auction->end_at?->format('Y-m-d\TH:i')) }}" required>
            @error('end_at') <span class="form__error">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="form__group form__group--check">
        <label>
            <input type="checkbox" name="publish" value="1"
                {{ old('publish', in_array($auction->status, ['pending_moderation', 'scheduled', 'active'])) ? 'checked' : '' }}>
            Отправить на модерацию (администратор одобрит лот, и он откроется по расписанию)
        </label>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn btn--primary">
            {{ $auction->exists ? 'Сохранить' : 'Создать лот' }}
        </button>
        <a href="{{ route('moderator.auctions.index') }}" class="btn btn--ghost">Отмена</a>
    </div>
</form>
@endsection
