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
            <th>Wielkosc zbiornika</th>
            <th>Wywiezione m3</th>
            <th  style="padding-left: 20px; padding-right: 20px;">Zlewnia</th>
            <th>Zaplacono</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jobs as $job)
            <tr>
                <th>{{$job->id}}</th>
                <th>{{$job->address->adres ?? ''}} {{$job->address->numer ?? ''}}</th>
                <th>{{$job->address->miasto ?? ''}}</th>
                <th>{{$job->address->zbiornik ?? ''}}</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        @endforeach
    </tbody>
</table>
</body>