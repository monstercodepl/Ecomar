<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Raport Miesięczny</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        h1, h4 { text-align: center; }
    </style>
</head>
<body>
    <h1>Raport Miesięczny</h1>

    @if($addressId)
        <p style="text-align: center;">Adres: {{$aggregatedJobs[0]->address->adres}} {{$aggregatedJobs[0]->address->numer}}, {{$aggregatedJobs[0]->address->miasto}}</p>
    @endif
    @if($dateFrom || $dateTo)
        <p style="text-align: center;">Zakres dat: {{ $dateFrom ?? 'Dowolna' }} – {{ $dateTo ?? 'Dowolna' }}</p>
    @endif

    <table>
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

    <div style="margin-top: 20px; text-align: center;">
        <h4>Łączna liczba zleceń: {{ $totalJobs }}</h4>
        <h4>Łączna kwota: {{ number_format($totalAmount, 2) }} PLN</h4>
        <h4>Łącznie wypompowane szambo: {{ number_format($totalPumped, 2) }} m³</h4>
    </div>
</body>
</html>
