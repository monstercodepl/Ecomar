@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Raport Miesięczny</h1>

    <!-- Link do pobrania PDF -->
    <a href="{{ route('reports.download', request()->query()) }}" class="btn btn-success mb-3">Pobierz jako PDF</a>

    <!-- Formularz filtra -->
    <form action="{{ route('reports.index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label for="address_id">Filtruj według adresu (opcjonalnie)</label>
                <select name="address_id" id="address_id" class="form-control select-2">
                    <option value="">Wszystkie adresy</option>
                    @foreach($addresses as $addr)
                        <option value="{{ $addr->id }}" {{ (isset($addressId) && $addressId == $addr->id) ? 'selected' : '' }}>
                            {{ $addr->adres }} {{ $addr->numer }}, {{ $addr->miasto }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="date_from">Data od</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-4">
                <label for="date_to">Data do</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4">
                <label>
                    <input type="checkbox" name="show_comments" value="1" {{ $showComments ? 'checked' : '' }}>
                    Pokaż komentarze
                </label>
            </div>
            <div class="col-md-4">
                <label>
                    <input type="checkbox" name="show_price" value="1" {{ $showPrice ? 'checked' : '' }}>
                    Pokaż cenę
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Zastosuj filtry</button>
    </form>

    <!-- Tabela zleceń -->
    <table class="table table-bordered">
        <thead>
            <tr>
                @if($addressId)
                    <!-- Tryb z konkretnym adresem -->
                    <th>ID</th>
                    <th>Data wykonania</th>
                    <th>Adres</th>
                    <th>Liczba zleceń w grupie</th>
                    <th>Wypompowane (m³)</th>
                    @if($showPrice)
                        <th>Cena</th>
                    @endif
                    @if($showComments)
                        <th>Komentarz</th>
                    @endif
                @else
                    <!-- Tryb dla wszystkich adresów -->
                    <th>Adres</th>
                    <th>Liczba zleceń</th>
                    <th>Wypompowane (m³)</th>
                    @if($showPrice)
                        <th>Cena</th>
                    @endif
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($aggregatedJobs as $job)
                @if($addressId)
                    <tr>
                        <td>
                            @if(!empty($job->id))
                                <a href="{{ route('job', $job->id) }}">{{ $job->id }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if(!empty($job->schedule))
                                {{ \Carbon\Carbon::parse($job->schedule)->format('Y-m-d') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($job->address)
                                {{ $job->address->adres }} {{ $job->address->numer }}, {{ $job->address->miasto }}
                            @else
                                Brak
                            @endif
                        </td>
                        <td>{{ $job->count }}</td>
                        <td>{{ $job->pumped }}</td>
                        @if($showPrice)
                            <td>{{ $job->price }}</td>
                        @endif
                        @if($showComments)
                            <td>{{ $job->comment }}</td>
                        @endif
                    </tr>
                @else
                    <tr>
                        <td>
                            @if($job->address)
                                {{ $job->address->adres }} {{ $job->address->numer }}, {{ $job->address->miasto }}
                            @else
                                Brak
                            @endif
                        </td>
                        <td>{{ $job->count }}</td>
                        <td>{{ $job->pumped }}</td>
                        @if($showPrice)
                            <td>{{ $job->price }}</td>
                        @endif
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Podsumowanie raportu -->
    <div class="mt-3">
        <h4>Łączna liczba zleceń: {{ $totalJobs }}</h4>
        <h4>Łączna kwota: {{ number_format($totalAmount, 2) }} PLN</h4>
        <h4>Łącznie wypompowane szambo: {{ number_format($totalPumped, 2) }} m³</h4>
    </div>
</div>
@endsection
