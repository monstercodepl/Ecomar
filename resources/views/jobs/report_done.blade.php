<html>
<style>
table, th, td {
  border:1px solid black;
}
</style>
<body>
<table>
    Data: {{$date}}
    <thead>
        <tr>
            <th>LP</th>
            <th>WZ</th>
            <th>Ulica</th>
            <th>Miejscowosc</th>
            <th>Wywiezione m3</th>
            <th>Kwota</th>
            <th>Rodzaj płatności</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jobs as $job)
            <tr>
                <th>{{$job->id}}</th>
                <th>@if($job->wz){{($job->wz->letter ?? '').($job->wz->number ?? '').'/'.($job->wz->month ?? '').'/'.($job->wz->year ?? '')}}@endif</th>
                <th>{{$job->address->adres ?? ''}} {{$job->address->numer ?? ''}}</th>
                <th>{{$job->address->miasto ?? ''}}</th>
                <th>{{$job->pumped ?? ''}}</th>
                <th>@if(!$job->address->user->nip){{$job->price ?? ''}}@endif</th>
                <th>@if($job->cash)Gotówka @else Przelew @endif</th>
            </tr>
        @endforeach
    </tbody>
</table>
</body>