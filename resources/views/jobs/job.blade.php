@extends('layouts.user_type.auth')

@section('content')
<div class="row">
    <div class="container-fluid">
        <div class="card card-body blur shadow-blur">
            <div class="row">
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            Nowe zlecenie
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0 px-3">
                <h6 class="mb-0">Dane</h6>
            </div>
            <div class="card-body pt-4 p-3">
                <form action="/job" method="POST" role="form text-left">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="id" value="{{$job->id}}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Adres</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="address" id="job_2" class="form-control" disabled>
                                            <option value="{{$job->address->id}}">{{$job->address->adres}} {{$job->address->numer}}, {{$job->address->miasto}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Kierowca</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="driver" id="new_job_2" class="form-control select-2">
                                            <option value=""></option>
                                        @foreach($drivers as $driver)
                                            <option value="{{$driver->id}}" @if($job->driver_id == $driver->id) selected @endif>{{$driver->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Data</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="datetime-local"  id="number" name="date" value="{{$job->schedule}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">Zapisz</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection