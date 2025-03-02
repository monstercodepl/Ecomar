@extends('layouts.user_type.auth')

@section('content')

<div>
    <h5 class="mb-4">Wszystkie zlecenia</h5>

    <!-- Formularz filtrowania po dacie -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="start_date">Data początkowa:</label>
            <input type="date" id="start_date" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="end_date">Data końcowa:</label>
            <input type="date" id="end_date" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button id="filterButton" class="btn btn-primary">Filtruj</button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-flex flex-row justify-content-between">
                <div>
                    <h5 class="mb-0">Wszystkie zlecenia</h5>
                </div>
                <a href="{{ route('create-job') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">
                    +&nbsp; Nowe zlecenie
                </a>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="jobs-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="text-center">Adres</th>
                            <th class="text-center">Klient</th>
                            <th class="text-center">Kierowca</th>
                            <th class="text-center">Data</th>
                            <th class="text-center">WZ</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Komentarz</th>
                            <th class="text-center">Akcje</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables JavaScript -->
<script>
$(document).ready(function() {
    var table = $('#jobs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("jobs.data") }}',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'id', name: 'jobs.id' },
            { data: 'address.adres', name: 'address.adres', defaultContent: '' },
            { data: 'address', name: 'address.user.name', defaultContent: 'brak' },
            { data: 'driver.name', name: 'driver.name', defaultContent: '' },
            { data: 'schedule', name: 'jobs.schedule', render: function(data) { return data.substr(0, 10); }},
            { 
                data: 'wz', 
                name: 'wzs.letter',  // kolumna z joinowanej tabeli wzs
                defaultContent: '',
                // render: function(data, type, row) {
                //     dd(row);
                //     return row.wz_letter + row.wz_number + '/' + row.wz_month + '/' + row.wz_year;
                // }
            },
            { data: 'status', name: 'jobs.status' },
            { data: 'comment', name: 'jobs.comment', defaultContent: '' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    // Obsługa filtrowania po dacie
    $('#filterButton').click(function() {
        table.ajax.reload();
    });
});
</script>

@endsection
