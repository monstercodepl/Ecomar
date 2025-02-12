<html>
    <head>
        <meta charset="utf-8">
        <title>Lista Zadań</title>
        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
                padding: 5px;
            }
            caption {
                font-weight: bold;
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        @php
            $sortedJobs = $jobs->sortBy(function($job) {
                return $job->address->adres ?? '';
            });
        @endphp

        <table>
            <caption>Data: {{ $date }}</caption>
            <thead>
                <tr>
                    <th>LP</th>
                    <th>ID</th>
                    <th>Ulica</th>
                    <th>Miejscowość</th>
                    <th>Wielkość zbiornika</th>
                    <th>Komentarz</th>
                    <th>Wywiezione m3</th>
                    <th style="padding-left: 20px; padding-right: 20px;">Zlewnia</th>
                    <th>Zapłacono</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sortedJobs as $job)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $job->id }}</td>
                        <td>{{ $job->address->adres ?? '' }} {{ $job->address->numer ?? '' }}</td>
                        <td>{{ $job->address->miasto ?? '' }}</td>
                        <td>{{ $job->address->zbiornik ?? '' }}</td>
                        <td>{{ $job->comment ?? ''}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
