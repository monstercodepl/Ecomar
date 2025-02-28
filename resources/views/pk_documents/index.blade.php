@extends('layouts.user_type.auth')

@section('content')
<div>
    <h1>Dokumenty PK</h1>
    <a href="{{ route('pk_documents.create') }}" class="btn btn-success mb-3">Dodaj dokument PK</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Numer Dokumentu</th>
                <th>Data Utworzenia</th>
                <th>Użytkownik</th>
                <th>Wartość Korekty</th>
                <th>Komentarz</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pkDocuments as $pk)
                <tr>
                    <td>{{ $pk->letter }}{{ $pk->number }}/{{ $pk->month }}/{{ $pk->year }}</td>
                    <td>{{ $pk->created_at->format('Y-m-d') }}</td>
                    <td>{{ $pk->user->name }}</td>
                    <td>{{ number_format($pk->adjustment_value, 2) }}</td>
                    <td>{{ $pk->comment }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Brak dokumentów PK</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
