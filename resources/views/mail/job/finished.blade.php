Dziękujemy za skorzystanie z usługi asemizacyjnej. <br><br>
Data: {{$job->updated_at}} <br><br>
Numer konta do płatności: 62 1090 1450 0000 0001 2603 4343 <br><br>
Tytuł: {{$job->address->adres ?? ''}} {{$job->address->numer ?? ''}}, {{$job->address->miasto}} <br><br>
Kwota do zapłaty: {{$job->price}} zł<br><br>
Wypompowano: {{$job->pumped}} m3<br><br>
Pozdrawiamy