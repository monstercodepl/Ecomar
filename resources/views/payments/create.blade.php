@extends('layouts.user_type.auth')

@section('content')
<div>
    <h1>Dodaj Dokument Płatności</h1>
    <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Pole "Numer Płatności" zostało usunięte, ponieważ korzystamy z automatycznego id --}}

        <div class="form-group">
            <label for="wz_id">Powiąż z dokumentem WZ (opcjonalnie)</label>
            <select name="wz_id" id="wz_id" class="form-control">
                <option value="">-- Brak --</option>
                @foreach($wzs as $wz)
                    <option value="{{ $wz->id }}">
                        {{ $wz->letter }}{{ $wz->number }}/{{ $wz->month }}/{{ $wz->year }} - {{ $wz->client_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="user_id">Użytkownik (płatnik)</label>
            <select name="user_id" id="user_id" class="form-control" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Kwota</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
        </div>

        <div class="form-group">
            <label for="payment_date">Data Płatności</label>
            <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date') }}" required>
        </div>

        <div class="form-group">
            <label for="method">Metoda Płatności</label>
            <select name="method" id="method" class="form-control" required>
                <option value="przelew">Przelew</option>
                <option value="gotówka">Gotówka</option>
                <!-- Możesz dodać inne opcje -->
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status Płatności</label>
            <select name="status" id="status" class="form-control" required>
                <option value="pending">Oczekujące</option>
                <option value="confirmed">Potwierdzone</option>
                <option value="rejected">Odrzucone</option>
            </select>
        </div>

        <div class="form-group">
            <label for="document">Dokument (opcjonalnie)</label>
            <input type="file" name="document" id="document" class="form-control">
        </div>

        <br>
        <button type="submit" class="btn btn-primary">Dodaj Dokument Płatności</button>
    </form>
</div>
@endsection
