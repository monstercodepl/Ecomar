@extends('layouts.user_type.auth')

@section('content')
<div>
    <h1>Dokumenty Księgowe</h1>
    <form method="GET" action="{{ route('billings.index') }}">
        <div class="row">
            <div class="col-md-4">
                <label for="date_from">Data od:</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-4">
                <label for="date_to">Data do:</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-4">
                <label for="user_id">Użytkownik:</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Wszyscy</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Filtruj</button>
    </form>
    
    <!-- Przyciski akcji -->
    <div class="mb-3">
        <a href="{{ route('payments.create') }}" class="btn btn-success">
            Dodaj dokument płatności
        </a>
        <a href="{{ route('pk_documents.create') }}" class="btn btn-info">
            Dodaj dokument PK
        </a>
    </div>
    
    @php
        // Obliczamy saldo dla dokumentów WZ (billings)
        $totalWZSaldo = 0;
        foreach ($billings as $wz) {
            if ($wz->cash) {
                // Jeśli WZ opłacone gotówką: 
                // - bez wpisu w payments: saldo = 0,
                // - z wpisami: saldo = suma płatności (nadpłata/dodatkowe wpłaty)
                $saldo = $wz->payments->isEmpty() ? 0 : $wz->payments->sum('amount');
            } else {
                // Dla WZ nie opłaconych gotówką:
                $saldo = $wz->payments->isNotEmpty() ? $wz->payments->sum('amount') - $wz->price : -$wz->price;
            }
            $totalWZSaldo += $saldo;
        }
        
        // Suma płatności niezwiązanych z WZ (orphan payments)
        $orphanTotal = $orphanPayments->sum('amount');
        
        // Suma dokumentów PK – wartość korekty (może być dodatnia lub ujemna)
        $pkTotal = $pkDocuments->sum('adjustment_value');
        
        // Łączne saldo obejmuje saldo z WZ, orphan payments oraz korekty PK
        $overallSaldo = $totalWZSaldo + $orphanTotal + $pkTotal;
    @endphp

    <div class="mb-3">
        <h4>
            Podsumowanie salda: 
            <span class="{{ $overallSaldo < 0 ? 'text-danger' : 'text-success' }}">
                {{ $overallSaldo < 0 ? '-' : '' }}{{ number_format(abs($overallSaldo), 2) }}
            </span>
        </h4>
    </div>

    <!-- Tabela dokumentów WZ z przypisanymi płatnościami -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <!-- Kolumny WZ -->
                <th>Numer WZ</th>
                <th>Data WZ</th>
                <th>Klient</th>
                <th>Ilość</th>
                <th>Cena</th>
                <th>Status WZ</th>
                <th>Saldo</th>
                <!-- Kolumny płatności -->
                <th>Data Płatności</th>
                <th>Kwota</th>
                <th>Metoda</th>
                <th>Status Płatności</th>
            </tr>
        </thead>
        <tbody>
            @forelse($billings as $wz)
                @php
                    // Pobieramy i sortujemy płatności przypisane do WZ według daty
                    $payments = $wz->payments->sortBy('payment_date');
                    if ($wz->cash) {
                        $saldo = $payments->isEmpty() ? 0 : $payments->sum('amount');
                    } else {
                        $saldo = $payments->isNotEmpty() ? $payments->sum('amount') - $wz->price : -$wz->price;
                    }
                @endphp

                @if($wz->cash && $payments->isEmpty())
                    {{-- WZ opłacone gotówką bez wpisu w payments --}}
                    <tr>
                        <td>{{ $wz->letter }}{{ $wz->number }}/{{ $wz->month }}/{{ $wz->year }}</td>
                        <td>{{ $wz->created_at->format('Y-m-d') }}</td>
                        <td>{{ $wz->client_name }}</td>
                        <td>{{ $wz->amount }}</td>
                        <td>{{ $wz->price }}</td>
                        <td>{{ $wz->paid ? 'Opłacone' : 'Nieopłacone' }}</td>
                        <td>
                            <span class="text-success">
                                {{ number_format(0, 2) }}
                            </span>
                        </td>
                        <td colspan="4" class="text-center">
                            Opłacono gotówką (brak wpisu w payments)
                        </td>
                    </tr>
                @elseif($payments->isNotEmpty())
                    @php $rowCount = $payments->count(); @endphp
                    @foreach($payments as $index => $payment)
                        <tr>
                            @if($index == 0)
                                <td rowspan="{{ $rowCount }}">{{ $wz->letter }}{{ $wz->number }}/{{ $wz->month }}/{{ $wz->year }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $wz->created_at->format('Y-m-d') }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $wz->client_name }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $wz->amount }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $wz->price }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $wz->paid ? 'Opłacone' : 'Nieopłacone' }}</td>
                                <td rowspan="{{ $rowCount }}">
                                    <span class="{{ $saldo < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $saldo < 0 ? '-' : '' }}{{ number_format(abs($saldo), 2) }}
                                    </span>
                                </td>
                            @endif
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                            <td>{{ $payment->amount }}</td>
                            <td>{{ $payment->method }}</td>
                            <td>{{ ucfirst($payment->status) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $wz->letter }}{{ $wz->number }}/{{ $wz->month }}/{{ $wz->year }}</td>
                        <td>{{ $wz->created_at->format('Y-m-d') }}</td>
                        <td>{{ $wz->client_name }}</td>
                        <td>{{ $wz->amount }}</td>
                        <td>{{ $wz->price }}</td>
                        <td>{{ $wz->paid ? 'Opłacone' : 'Nieopłacone' }}</td>
                        <td>
                            <span class="{{ $saldo < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $saldo < 0 ? '-' : '' }}{{ number_format(abs($saldo), 2) }}
                            </span>
                        </td>
                        <td colspan="4" class="text-center">
                            @if($wz->cash)
                                Opłacono gotówką (brak wpisu w payments)
                            @else
                                Brak dokumentu płatności
                            @endif
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="11">Brak dokumentów księgowych.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Sekcja orphan payments -->
    @if($orphanPayments->isNotEmpty())
        <h2>Płatności niezwiązane z dokumentami WZ</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Płatności</th>
                    <th>Data Płatności</th>
                    <th>Kwota</th>
                    <th>Metoda</th>
                    <th>Status Płatności</th>
                    <th>Użytkownik</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orphanPayments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->method }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                        <td>{{ $payment->user->name ?? 'Brak' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <!-- Sekcja dokumentów PK -->
    <h2>Dokumenty PK</h2>
    <div class="mb-3">
        <a href="{{ route('pk_documents.create') }}" class="btn btn-info">
            Dodaj dokument PK
        </a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Numer PK</th>
                <th>Data PK</th>
                <th>Użytkownik</th>
                <th>Wartość Korekty</th>
                <th>Komentarz</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pkDocuments as $pk)
                <tr>
                    <td>{{ $pk->letter }}{{ $pk->number }}/{{ $pk->month }}/{{ $pk->year }}</td>
                    <td>{{ $pk->created_at->format('Y-m-d') }}</td>
                    <td>{{ $pk->user->name }}</td>
                    <td>{{ number_format($pk->adjustment_value, 2) }}</td>
                    <td>{{ $pk->comment }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Brak dokumentów PK.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
</div>
@endsection
