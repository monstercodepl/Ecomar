@extends('layouts.user_type.auth')

@section('content')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script src='fullcalendar/core/locales/pl.global.js'></script>
<!-- Umieść ten kod w sekcji <head> lub po załadowaniu odpowiednich plików FullCalendar -->
<style>
   
    /* Zamiast usuwać, ukrywamy zawartość pierwszej komórki nagłówka,
       aby nie zaburzać układu kolumn – pierwszy element to oś czasu */
    .fc-col-header-cell:first-child {
      visibility: hidden;
    }
    /* Ukrywa sloty czasowe w głównej części kalendarza */
    .fc-timegrid-slot {
      display: none;
    }
    /* Usuwamy margines po lewej stronie slotów */
    .fc-timegrid-slots {
      margin-left: 0;
    }
    /* Opcjonalnie – usuwa zbędne odstępy w obszarze siatki */
    .fc-timegrid-body {
      min-height: 0;
    }
</style>

<!-- Umieść kontener, w którym będzie renderowany kalendarz -->
<div class="row"><div id="calendar"></div></div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'pl',
      firstDay: 1,
      initialView: 'timeGridWeek', // używamy widoku tygodniowego
      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      buttonText: {
        today: 'Dziś',
        month: 'Miesiąć',
        week: 'Tydzień',
        day: 'Dzień',
        list: 'Lista'
      },
      displayEventTime: false, // Ukrywa godziny przy wydarzeniach
      allDaySlot: true,        // Włącza strefę wydarzeń całodniowych
      // Upewnij się, że zdarzenia są dodawane jako całodniowe (allDay: true)
      events: [
        @foreach($jobs as $job)
          {
            title: "{{$job->address->adres ?? ''}} {{$job->address->numer ?? 'brak'}}, {{$job->address->miasto ?? 'brak'}}",
            start: '{{$job->schedule}}',
            allDay: true,   // Dzięki temu wydarzenie trafia do strefy całodniowej
            url: '/job/{{$job->id}}'
          },
        @endforeach
      ]
    });

    calendar.render();
  });
</script>


@endsection
@push('dashboard')
  <script>
    
  </script>
@endpush

