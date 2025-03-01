@extends('layouts.user_type.auth')

@section('content')
<div class="row">
    <div class="container-fluid">
        <div class="card card-body blur shadow-blur">
            <div class="row">
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            {{$address->address}}
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
                <form action="/address" method="POST" role="form text-left">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{$address->id}}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-name" class="form-control-label">Ulica</label>
                                <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Ulica" id="user-name" name="ulica" value="{{$address->adres}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-email" class="form-control-label">Numer</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="1" id="user-email" name="numer" value="{{$address->numer}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Gmina</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="municipality" id="address-1" class="form-control select-2">
                                        <option value=""></option>
                                        @foreach($municipalities as $municipality)
                                            <option @if($municipality->id == ($address->municipality->id ?? '')) selected @endif value="{{$municipality->id}}">{{$municipality->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Miasto</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Miasto" id="number" name="miasto" value="{{$address->miasto}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Zbiornik</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="number" placeholder="8" id="number" step="0.5" name="zbiornik" value="{{$address->zbiornik}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Strefa</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="zone_id" id="address-2" class="form-control select-2">
                                            <option value=""></option>
                                        @foreach($zones as $zone)
                                            <option @if($zone->id == $address->zone_id) selected @endif value="{{$zone->id}}">{{$zone->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="user.phone" class="form-control-label">Użytkownik</label>
                            <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <select name="user_id" id="address-3" class="form-control select-2">
                                            <option value=""></option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}" @if($user->id === $address->user_id) selected @endif>{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.phone" class="form-control-label">Aglomeracja</label>
                                <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <input class="" type="checkbox" id="number" name="aglomeracja" @if($user->aglomeracja) checked @endif>
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