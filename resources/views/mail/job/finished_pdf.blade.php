
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
body{font-family: "DejaVu Sans", sans-serif;}
</style>
<body>
<div style="width: 500px;">
 <div style="width: 198px; text-align: center; float: left; padding-top: 19px; padding-bottom: 19px; border: 1px solid black">
   <b>ECO-MAR</b><br>
   <b>Sergii Maksimov</b></br>
   <b>ul. Wiśniowa 8</b><br>
   <b>62-025 Siekierki Wielkie</b><br>
   <b>NIP 777 324 21 34</b></br>
   <b>tel. 502 395 675</b>
 </div>
 <div style="width: 300px; float: left; text-align: center;"> 
   <h3>WYDANIE ZEWNĘTRZNE</h3>
   <p>Dnia {{$job->updated_at}}</p>
   <p>Numer {{$id}}</p>
 </div>
 <div style="width: 500px; clear: both; padding-top: 10px; padding-bottom: 10px;">
   Odbiorca: {{$job->address->user->name ?? ''}}<br>
   {{$job->address->adres ?? ''}} {{$job->address->numer ?? ''}}, {{$job->address->miasto}}
 </div>
 <div style="width: 488px; text-align: center; border: 1px solid black";>
   <p>nazwa usługi: USŁUGA ASENIZACYJNA </p>
   <p>wartość: {{$job->price}} zł</p>
   <p>wypompowano: {{$job->pumped}} m3</p>
   <p>forma płatności: @if($job->cash)zapłacono gotówką @else przelew @endif</p>

@if(!$job->cash)<p>Przelew proszę dokonać na nr rach.: 62 1090 1450 0000 0001 2603 4343 termin płatności przelewem wynosi 7 dni. <br>
 <b> tytułem: numer dokumentu / adres usługi / data usługi
</p>@endif
 </div>
</div>
</body>

