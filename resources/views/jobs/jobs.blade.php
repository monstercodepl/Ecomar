@extends('layouts.user_type.auth')

@section('content')

<div class="container">
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

    <!-- Tabela DataTables -->
    <div class="table-responsive">
        <table class="table table-bordered" id="jobs-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Adres</th>
                    <th>Klient</th>
                    <th>Kierowca</th>
                    <th>Data</th>
                    <th>WZ</th>
                    <th>Status</th>
                    <th>Komentarz</th>
                    <th>Akcje</th>
                </tr>
            </thead>
        </table>
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
            { data: 'id', name: 'id' },
            { data: 'address.adres', name: 'address.adres', defaultContent: '' },
            { data: 'address.user.name', name: 'address.user.name', defaultContent: 'brak' },
            { data: 'driver.name', name: 'driver.name', defaultContent: '' },
            { data: 'schedule', name: 'schedule', render: function(data) { return data.substr(0, 10); }},
            { data: 'wz', name: 'wz', defaultContent: '', render: function(data, type, row) {
                return data ? `${data.letter || ''}${data.number || ''}/${data.month || ''}/${data.year || ''}` : '';
            }},
            { data: 'status', name: 'status' },
            { data: 'comment', name: 'comment', defaultContent: '' },
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
