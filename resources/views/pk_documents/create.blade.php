@extends('layouts.user_type.auth')

@section('content')
<div>
    <h1>Dodaj Dokument PK</h1>
    <form method="POST" action="{{ route('pk_documents.store') }}">
        @csrf
        <div class="form-group">
            <label for="letter">Litera (domyślnie "P")</label>
            <input type="text" name="letter" id="letter" class="form-control" value="{{ old('letter', 'P') }}">
        </div>
        {{-- Pole "numer" jest generowane automatycznie, więc nie ma go w formularzu --}}
        <div class="form-group">
            <label for="month">Miesiąc</label>
            <input type="text" name="month" id="month" class="form-control" value="{{ old('month') }}" required>
        </div>
        <div class="form-group">
            <label for="year">Rok</label>
            <input type="text" name="year" id="year" class="form-control" value="{{ old('year') }}" required>
        </div>
        <div class="form-group">
            <label for="user_id">Użytkownik</label>
            <select name="user_id" id="user_id" class="form-control" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="adjustment_value">Wartość korekty</label>
            <input type="number" step="0.01" name="adjustment_value" id="adjustment_value" class="form-control" value="{{ old('adjustment_value') }}" required>
        </div>
        <div class="form-group">
            <label for="comment">Komentarz</label>
            <textarea name="comment" id="comment" class="form-control">{{ old('comment') }}</textarea>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Dodaj Dokument PK</button>
    </form>
</div>
@endsection
