@extends('layouts.user_type.auth')

@section('content')
<div class="row">
    <div class="container-fluid">
        <div class="card card-body blur shadow-blur">
            <div class="row">
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            Edytuj wydanie {{$wz->letter}}{{$wz->number}}/{{$wz->month}}/{{$wz->year}}
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
                <form action="/wz-save" method="POST" role="form text-left">
                    @csrf
                    @method('POST')
                    <input type='hidden' name="id" value="{{$wz->id}}" />
                    <div class="row">
                        <div class="col-md-6">
                            <label for="user-name" class="form-control-label">Ilość</label>
                                <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Ilość" id="user-name" name="amount" value="{{$wz->amount ?? ''}}">
                                </div>
                        </div>
                        <div class="col-md-6">
                            <label for="user-name" class="form-control-label">Cena</label>
                                <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Cena" id="user-name" name="price" value="{{$wz->price ?? ''}}">
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