Dziękujemy za skorzystanie z usługi asenizacyjnej. <br><br>
Data: {{$job->updated_at}} <br><br>
@if(!$job->cash)Numer konta do płatności: 62 1090 1450 0000 0001 2603 4343 <br><br>@endif
Tytuł: {{$job->address->adres ?? ''}} {{$job->address->numer ?? ''}}, {{$job->address->miasto}} <br><br>
Kwota do zapłaty: {{$job->price}} zł @if($job->cash) (opłacono gotówką) @endif<br><br>
Wypompowano: {{$job->pumped}} m3<br><br>
Pozdrawiamy