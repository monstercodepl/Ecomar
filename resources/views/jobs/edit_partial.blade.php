@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Edycja zlecenia częściowego</h1>

    @if(empty($mainJob->partial) && \App\Models\Job::where('partial', $mainJob->id)->exists())
        <div class="alert alert-info">
            To zlecenie jest podzielone – edytując poniżej, zmodyfikujesz zarówno główne zlecenie, jak i jego drugą część.
        </div>
    @elseif(!empty($mainJob->partial))
        <div class="alert alert-info">
            To zlecenie jest drugą częścią podzielonego zlecenia i zostanie zaktualizowane razem z głównym.
        </div>
    @endif

    <form action="{{ route('jobs.updatePartial') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Formularz dla głównego zlecenia -->
            <div class="col-md-6">
                <h3>Główne zlecenie (ID: {{ $mainJob->id }})</h3>
                <input type="hidden" name="main_id" value="{{ $mainJob->id }}">
                <div class="form-group">
                    <label for="main_date">Data wykonania</label>
                    <input type="date" name="main_date" id="main_date" class="form-control" value="{{ \Carbon\Carbon::parse($mainJob->schedule)->format('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="main_driver">Kierowca</label>
                    <input type="text" name="main_driver" id="main_driver" class="form-control" value="{{ $mainJob->driver_id }}">
                </div>
                <div class="form-group">
                    <label for="main_comment">Komentarz</label>
                    <textarea name="main_comment" id="main_comment" class="form-control">{{ $mainJob->comment }}</textarea>
                </div>
                <div class="form-group">
                    <label for="main_pumped">Wypompowane (m³)</label>
                    <input type="number" step="0.01" name="main_pumped" id="main_pumped" class="form-control" value="{{ $mainJob->pumped }}" required>
                </div>
                <div class="form-group">
                    <label for="main_price">Cena</label>
                    <input type="number" step="0.01" name="main_price" id="main_price" class="form-control" value="{{ $mainJob->price }}" required>
                </div>
            </div>

            <!-- Formularz dla drugiej części (jeśli istnieje) -->
            <div class="col-md-6">
                @if($secondJob)
                    <h3>Druga część zlecenia (ID: {{ $secondJob->id }})</h3>
                    <input type="hidden" name="second_id" value="{{ $secondJob->id }}">
                    <div class="form-group">
                        <label for="second_date">Data wykonania</label>
                        <input type="date" name="second_date" id="second_date" class="form-control" value="{{ \Carbon\Carbon::parse($secondJob->schedule)->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label for="second_driver">Kierowca</label>
                        <input type="text" name="second_driver" id="second_driver" class="form-control" value="{{ $secondJob->driver_id }}">
                    </div>
                    <div class="form-group">
                        <label for="second_comment">Komentarz</label>
                        <textarea name="second_comment" id="second_comment" class="form-control">{{ $secondJob->comment }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="second_pumped">Wypompowane (m³)</label>
                        <input type="number" step="0.01" name="second_pumped" id="second_pumped" class="form-control" value="{{ $secondJob->pumped }}">
                    </div>
                    <div class="form-group">
                        <label for="second_price">Cena</label>
                        <input type="number" step="0.01" name="second_price" id="second_price" class="form-control" value="{{ $secondJob->price }}">
                    </div>
                @else
                    <div class="alert alert-warning">
                        Druga część zlecenia nie istnieje.
                    </div>
                @endif
            </div>
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
