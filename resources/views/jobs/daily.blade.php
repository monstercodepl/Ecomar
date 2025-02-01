@extends('layouts.user_type.auth')

@section('content')
<div class="row">
    <div class="container-fluid">
        <div class="card card-body blur shadow-blur">
            <div class="row">
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            Lista dzienna
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0 px-3">
                <h6 class="mb-0">Generuj liste</h6>
            </div>
            <div class="card-body pt-4 p-3">
                    <form action="/generate_report" method="POST" target="_blank" role="form text-left">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <label for="user.phone" class="form-control-label">Kierowca</label>
                                    <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                        <select name="driver_id" id="job_2" class="form-control">
                                                <option value=""></option>
                                                @foreach($drivers as $driver)
                                                <option value="{{$driver->id}}">{{$driver->name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="col-md-6">
                                <label for="user.phone" class="form-control-label">Dzień</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="date"  id="number" name="date">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">Generuj</button>
                    </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection