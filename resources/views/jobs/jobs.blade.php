@extends('layouts.user_type.auth')

@section('content')

<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Wszystkie zlecenia</h5>
                        </div>
                        <a href="{{route('create-job')}}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+&nbsp; Nowe zlecenie</a>
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
                                        Klient
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Kierowca
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Data
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        WZ
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Akcje
                                    </th>
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
                                            <p class="text-xs font-weight-bold mb-0">{{$job->address->user->name ?? 'brak'}} {{$job->address->user->email ?? 'brak'}}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->driver->name ?? ''}}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ substr($job->schedule, 0, 10) }}                                            </p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0"><a href="{{ route('wz.download', ['job' => $job->id]) }}">@if($job->wz){{($job->wz->letter ?? '').($job->wz->number ?? '').'/'.($job->wz->month ?? '').'/'.($job->wz->year ?? '')}}@endif</a></p>
                                        </td>
                                        
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{$job->status}}</p>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn bg-danger text-white btn-md" data-bs-toggle="modal" data-bs-target="#deleteModal{{$job->id}}">
                                                 Usuń
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="deleteModal{{$job->id}}" tabindex="-1" aria-labelledby="deleteModalLabel{{$job->id}}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{$job->id}}">Potwierdzenie usunięcia</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Czy na pewno chcesz usunąć to zlecenie?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                                                            <form method="POST" action="job/delete">
                                                                @csrf
                                                                <input type="hidden" name="job_id" value="{{$job->id}}">
                                                                <button type="submit" class="btn btn-danger">Usuń</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('job', array('id' => $job->id)) }}"><button class="btn bg-wargning btn-md">Edytuj</button></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection