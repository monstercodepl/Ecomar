@extends('layouts.user_type.auth')

@section('content')
<div class="row">
    <div class="container-fluid">
        <div class="card card-body blur shadow-blur">
            <div class="row">
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            Nowe wydanie
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
                <form action="/wz-create" method="POST" role="form text-left">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Adres</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="address" id="new_job_1" class="form-control select-2">
                                            <option value=""></option>
                                        @foreach($addresses as $address)
                                            <option value="{{$address->id}}">{{$address->adres}} {{$address->numer}}, {{$address->miasto}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Klienci</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="client" id="new_job_2" class="form-control select-2">
                                            <option value=""></option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Miesiąc</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="month" id="new_job_3" class="form-control select-2">
                                        <option value="1">Styczeń</option>
                                        <option value="2">Luty</option>
                                        <option value="3">Marzec</option>
                                        <option value="4">Kwiecień</option>
                                        <option value="5">Maj</option>
                                        <option value="6">Czerwiec</option>
                                        <option value="7">Lipiec</option>
                                        <option value="8">Sierpień</option>
                                        <option value="9">Wrzesień</option>
                                        <option value="10">Październik</option>
                                        <option value="11">Listopad</option>
                                        <option value="12">Grudzień</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Rok</label>
                                <input name="year" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Litera</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="letter" id="new_job_4" class="form-control select-2">
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                        <option value="F">F</option>
                                        <option value="G">G</option>
                                        <option value="O">O</option>
                                        <option value="Z">Z</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Ilość m3</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <input name="amount" id="new_job_4" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Kwota</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <input name="price" id="new_job_4" class="form-control" />
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