@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Edycja zlecenia</h1>

    <form action="{{ route('jobs.update', $job->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $job->id }}">

        <div class="form-group">
            <label for="date">Data wykonania</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ \Carbon\Carbon::parse($job->schedule)->format('Y-m-d') }}" required>
        </div>

        <div class="form-group">
            <label for="driver">Kierowca</label>
            <select name="driver" id="driver" class="form-control select-2">
                <option value=""></option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}" @if($job->driver_id == $driver->id) selected @endif>
                        {{ $driver->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="comment">Komentarz</label>
            <textarea name="comment" id="comment" class="form-control">{{ $job->comment }}</textarea>
        </div>

        <div class="form-group">
            <label for="pumped">Wypompowane (m³)</label>
            <input type="number" step="0.01" name="pumped" id="pumped" class="form-control" value="{{ $job->pumped }}">
        </div>

        <div class="form-group">
            <label for="price">Cena</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ $job->price }}">
        </div>

        <!-- Checkbox do aktualizacji przypisanej WZ -->
        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="update_wz" name="update_wz" value="1">
            <label class="form-check-label" for="update_wz">
                Aktualizuj przypisaną WZ
            </label>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Zapisz zmiany</button>
    </form>
</div>
@endsection
