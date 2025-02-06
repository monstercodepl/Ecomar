<html>
<head>
    <style>
        table, th, td {
          border:1px solid black;
        }
    </style>
</head>
<body>
    @php
        // Sortowanie kolekcji $jobs według numerycznej części WZ->number.
        $jobs = $jobs->sortBy(function($job) {
            return $job->wz ? (int)$job->wz->number : 0;
        });
    @endphp

    <table>
        <caption>Data: {{$date}}</caption>
        <thead>
            <tr>
                <th>LP</th>
                <th>WZ</th>
                <th>Ulica</th>
                <th>Miejscowość</th>
                <th>Wywiezione m3</th>
                <th>Kwota</th>
                <th>Rodzaj płatności</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach($jobs as $job)
                <tr>
                    <td>{{$job->id}}</td>
                    <td>
                        @if($job->wz)
                            {{ ($job->wz->letter ?? '') . ($job->wz->number ?? '') . '/' . ($job->wz->month ?? '') . '/' . ($job->wz->year ?? '') }}
                        @endif
                    </td>
                    <td>{{$job->address->adres ?? ''}} {{$job->address->numer ?? ''}}</td>
                    <td>{{$job->address->miasto ?? ''}}</td>
                    <td>{{$job->pumped ?? ''}}</td>
                    <td>
                        @if(!$job->address->user->nip)
                            {{$job->price ?? ''}}
                            @php
                                $total += $job->price;
                            @endphp
                        @endif
                    </td>
                    <td>@if($job->cash)Gotówka @else Przelew @endif</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right;">SUMA:</td>
                <td>{{ $total }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
