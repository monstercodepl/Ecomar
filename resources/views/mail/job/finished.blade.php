Dziękujemy za skorzystanie z usługi asenizacyjnej. <br><br>
Data: {{$wz->created_at}} <br><br>
@if(!$wz->cash)Numer konta do płatności: 62 1090 1450 0000 0001 2603 4343 <br><br>@endif
Tytuł: {{$wz->client_address}} <br><br>
Kwota do zapłaty: {{$wz->price}} zł @if($wz->cash) (opłacono gotówką) @endif<br><br>
Wypompowano: {{$wz->pumped}} m3<br><br>
Pozdrawiamy