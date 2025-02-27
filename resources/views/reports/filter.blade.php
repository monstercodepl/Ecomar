@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Generuj Raport</h1>
    <form action="{{ route('reports.index') }}" method="GET" class="mb-3">
        <div class="row">
            <!-- Filtr po adresie -->
            <div class="col-md-4">
                <label for="address_id">Filtruj według adresu (opcjonalnie)</label>
                <select name="address_id" id="address_id" class="form-control select-2">
                    <option value="">Wszystkie adresy</option>
                    @foreach($addresses as $address)
                        <option value="{{ $address->id }}">
                            {{ $address->adres }} ({{ $address->miasto }})
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Zakres dat -->
            <div class="col-md-4">
                <label for="date_from">Data od</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ old('date_from') }}">
            </div>
            <div class="col-md-4">
                <label for="date_to">Data do</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ old('date_to') }}">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4">
                <label>
                    <input type="checkbox" name="show_comments" value="1" {{ old('show_comments') ? 'checked' : '' }}>
                    Pokaż komentarze
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Generuj Raport</button>
    </form>
</div>
@endsection
