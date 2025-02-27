@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Raport Miesięczny</h1>

    <!-- Link do pobrania PDF -->
    <a href="{{ route('reports.download', request()->query()) }}" class="btn btn-success mb-3">Pobierz jako PDF</a>

    <!-- Formularz filtra (opcjonalny) -->
    <form action="{{ route('reports.index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label for="address_id">Filtruj według adresu (opcjonalnie)</label>
                <select name="address_id" id="address_id" class="form-control select-2">
                    <option value="">Wszystkie adresy</option>
                    @foreach($addresses as $addr)
                        <option value="{{ $addr->id }}" {{ (isset($addressId) && $addressId == $addr->id) ? 'selected' : '' }}>
                            {{$addr->adres}} {{$addr->numer}}, {{$addr->miasto}}
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
        </div>
        <button type="submit" class="btn btn-primary mt-3">Zastosuj filtry</button>
    </form>

    <!-- Tabela zleceń (grupowanych) -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Data wykonania</th>
                <th>Adres</th>
                <th>Wypompowane (m³)</th>
                <th>Cena</th>
                @if($showComments)
                    <th>Komentarz</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($aggregatedJobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($job->schedule)->format('Y-m-d') }}</td>
                    <td>
                        @if($job->address)
                            {{$job->address->adres}} {{$job->address->numer}}, {{$job->address->miasto}}
                        @else
                            Brak
                        @endif
                    </td>
                    <td>{{ $job->pumped }}</td>
                    <td>{{ $job->price }}</td>
                    @if($showComments)
                        <td>{{ $job->comment }}</td>
                    @endif
                </tr>
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
