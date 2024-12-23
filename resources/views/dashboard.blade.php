@extends('layouts.user_type.auth')

@section('content')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>

      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          }
        });
        calendar.render();
      });

    </script>

  <div class="row">
    <div id="calendar"></div>
  </div>

@endsection
@push('dashboard')
  <script>
    
  </script>
@endpush

