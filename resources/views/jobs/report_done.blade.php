<html>
<style>
table, th, td {
  border:1px solid black;
}
</style>
<body>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ulica</th>
            <th>Miejscowosc</th>
            <th>Wywiezione m3</th>
            <th>Wywiezione (korekta)</th>
            <th>Zlewnia</th>
            <th>Kierowca</th>
            <th>Zaplacono</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jobs as $job)
            <tr>
                <th>{{$job->id}}</th>
                <th>{{$job->address->adres ?? ''}} {{$job->address->numer ?? ''}}</th>
                <th>{{$job->address->miasto ?? ''}}</th>
                <th>{{$job->pumped ?? ''}}</th>
                <th>{{$job->corrected}}</th>
                <th>{{$job->catchment->name ?? ''}}</th>
                <th>{{$job->driver->name ?? ''}}</th>
                <th></th>
            </tr>
        @endforeach
    </tbody>
</table>
</body>