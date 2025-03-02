@extends('layouts.user_type.auth')

@section('content')
<div>
    <h5 class="mb-4">Wszystkie wydania (WZ)</h5>
    
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
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="wz-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="text-center">Numer</th>
                            <th class="text-center">Klient</th>
                            <th class="text-center">Adres</th>
                            <th class="text-center">Metry</th>
                            <th class="text-center">Cena</th>
                            <th class="text-center">Wysłano</th>
                            <th class="text-center">Zapłacono</th>
                            <th class="text-center">Akcje</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#wz-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("wz.data") }}',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'id', name: 'wzs.id' },
            { data: 'numer', name: 'numer' },
            { data: 'client_name', name: 'wzs.client_name' },
            { data: 'client_address', name: 'wzs.client_address' },
            { data: 'amount', name: 'wzs.amount' },
            { data: 'price', name: 'wzs.price' },
            { data: 'sent', name: 'wzs.sent', render: function(data) { return data ? 'Tak' : 'Nie'; } },
            { data: 'paid', name: 'wzs.paid', render: function(data) { return data ? 'Tak' : 'Nie'; } },
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