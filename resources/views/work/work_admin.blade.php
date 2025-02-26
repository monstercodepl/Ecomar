@extends('layouts.user_type.auth')

@section('content')
<style>
    @media only screen and (max-width: 600px) {
        .table td, .table th {
            white-space: normal;
        }
}
</style>
<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            @if(isset($user))<h4>{{$user->name ?? ''}}</h4>@endif
                            <h5 class="mb-0">Pojazd</h5>
                        </div>
                    </div>
                </div>
                <div class="row card-body pt-4 pb-2">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p><b>Numer rejestracyny: </b>{{$truck->registration ?? 'n/d'}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p><b>Numer vin: </b>{{$truck->vin ?? 'n/d'}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p><b>Numer polisy: </b>{{$truck->oc_number ?? 'n/d'}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p><b>Data polisy: </b>{{$truck->oc_date ?? 'n/d'}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p><b>Data przeglądu: </b>{{$truck->inspection_date ?? 'n/d'}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p><b>Pojemność: </b>{{$truck->capacity ?? 'n/d'}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p><b>Zapełnienie: </b>{{$truck->amount ?? 'n/d'}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        @if($truck)
<div class="col-md-6">
    <div class="form-group">
        <form id="dump-form" method="POST" action="/work/dump">
            @csrf
            <input type="hidden" name="truck_id" value="{{$truck->id}}">
            @if(isset($user))
                <input type="hidden" name="user" value="{{$user->id}}">
            @endif
            Zlewnia: 
            <select name="catchment_id" id="dump-catchment" class="form-control">
                <option value=""></option>
                @foreach($catchments as $catchment)
                    <option value="{{$catchment->id}}">{{$catchment->name}}</option>
                @endforeach
            </select><br>
            Zlano: <input type="number" name="amount" id="dump-amount" step="0.01" min="0" class="form-control">
            <button type="button" class="btn bg-gradient-dark btn-md mt-4 mb-4" onclick="confirmDump()">Zapisz</button>
        </form>
    </div>
</div>
@endif

<!-- Modal Potwierdzenia dla zlewania -->
<div class="modal fade" id="confirmDumpModal" tabindex="-1" aria-labelledby="confirmDumpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDumpModalLabel">Potwierdzenie</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Zamknij">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span id="confirmDumpMessage"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
        <button type="button" class="btn btn-primary" id="confirmDumpButton">Tak, potwierdzam</button>
      </div>
    </div>
  </div>
</div>

<script>
function confirmDump() {
    let amount = document.getElementById('dump-amount').value;
    let catchment = document.getElementById('dump-catchment');
    let catchmentText = catchment.options[catchment.selectedIndex].text;

    if (!amount || amount <= 0) {
        alert('Podaj poprawną ilość m³!');
        return;
    }
    if (!catchment.value) {
        alert('Wybierz zlewnię!');
        return;
    }

    document.getElementById('confirmDumpMessage').innerText = `Czy na pewno chcesz zlać ${amount} m³ w zlewni: ${catchmentText}?`;
    let modal = new bootstrap.Modal(document.getElementById('confirmDumpModal'));
    modal.show();

    document.getElementById('confirmDumpButton').onclick = function () {
        document.getElementById('dump-form').submit();
    };
}
</script>


                        </div>
                    </div>
                    <div class="col-6">
                    @foreach($truck_jobs as $truck_job)
                        <b>ID: </b>{{$truck_job->id}}</br>
                        <b>Klient: </b>{{$truck_job->address->user->name}}</br>
                        <b>Adres: </b>{{$truck_job->address->adres ?? 'brak'}}, {{$truck_job->address->miasto ?? ''}}</br>
                        <b>Wypompowane: </b>{{$truck_job->pumped}}</br><br>
                    @endforeach   
                </div>
                </div>
                
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
        <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Zlecenia na samochodzie</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                ID
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Adres
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Zbiornik
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Wypompowano
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($current_jobs  as $job)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->id}}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->address->adres ?? ''}} {{$job->address->numer ?? 'brak'}}, {{$job->address->miasto ?? ''}}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->address->zbiornik ?? 'brak'}}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->pumped}}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Zlecenia</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        ID
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Adres
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Data
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Zbiornik
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Akcje
                                    </th>
                                    @can('give_job')
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        
                                    </th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jobs as $job)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->id}}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->address->adres ?? ''}} {{$job->address->numer ?? 'brak'}}, {{$job->address->miasto ?? ''}}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->schedule}}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->address->zbiornik ?? 'brak'}}</p>
                                        </td>
                                        <td class="text-center">
                                        <form  id="pump-form-{{$job->id}}" method="POST" action="/work/pump">
                                        @csrf
                                            <input class="form-control" type="hidden" name="job_id" value="{{$job->id}}">
                                            @if(isset($user))<input type="hidden" name="user" value="{{$user->id}}"></h4>@endif
                                            Wypompowano:<br> <input class="form-control mb-0" type="text" step="0.5" min="0" name="amount" id="amount-{{$job->id}}"><br>
                                            @if(!$job->partial) Częściowe <input class=" mt-2" type="checkbox" name="partial"><br>@endif
                                            Zapłacono gotówką <input class=" mt-2" type="checkbox" name="cash"><br>
                                            <button class="btn bg-gradient-light btn-md mt-3" type="button" onclick="confirmPump({{$job->id}})"> Wyślij </button>
                                        </form>
                                        </td>
                                        @can('give_job')
                                        <td class="text-center">
                                            <form method="POST" action="/work/give">
                                                @csrf
                                                <input class="form-control" type="hidden" name="job_id" value="{{$job->id}}">   
                                                Przekaż do: </br>
                                                <select name="driver_id" class="form-control">
                                                <option value=""></option>
                                                @foreach($drivers as $driver)
                                                <option value="{{$driver->id}}">{{$driver->name}}</option>
                                                @endforeach
                                                <input class="btn bg-gradient-light btn-md mt-3" type="submit">
                                        </select>
                                            </form>
                                        </td>
                                        @endcan
                                    </tr>
                                @endforeach
                      
<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Potwierdzenie</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Zamknij">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Czy na pewno chcesz wypompować <span id="confirmAmount"></span> m³?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
        <button type="button" class="btn btn-primary" id="confirmButton">Tak, potwierdzam</button>
      </div>
    </div>
  </div>
</div>

<script>
function confirmPump(jobId) {
    let amount = document.getElementById('amount-' + jobId).value;
    if (!amount || amount <= 0) {
        alert('Podaj poprawną ilość m³!');
        return;
    }

    document.getElementById('confirmAmount').innerText = amount;
    let modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();

    document.getElementById('confirmButton').onclick = function () {
        document.getElementById('pump-form-' + jobId).submit();
        document.getElementById('confirmButton').disabled = true;
    };
}
</script>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection