<div>
    <b>Numer zlecenia: </b>{{$job->id}}<br>
    <b>Data wykonanie zlecenia: </b>{{$job->updated_at}}<br>
    <b>Odbiorca: </b>{{$job->address->user->name ?? ''}}<br>
    <b>Adres odbiorcy: </b>{{$job->address->adres ?? ''}} {{$job->address->numer ?? ''}}, {{$job->address->miasto}}<br>
    <b>Ilość: </b>{{$job->pumped}}
</div>