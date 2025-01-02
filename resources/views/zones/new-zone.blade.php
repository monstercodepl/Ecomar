@extends('layouts.user_type.auth')

@section('content')
<div class="row">
    <div class="container-fluid">
        <div class="card card-body blur shadow-blur">
            <div class="row">
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            Nowa strefa
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
                <form action="/zone/new" method="POST" role="form text-left">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-name" class="form-control-label">Nazwa</label>
                                <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Nazwa" id="user-name" name="name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-email" class="form-control-label">Cena</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="number" placeholder="15" id="user-email" name="price">
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