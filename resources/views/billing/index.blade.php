@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Document List</h1>

    <!-- Formularz filtrowania -->
    <form action="{{ route('billing.index') }}" method="GET" class="mb-3">
        <div class="row">
            <!-- Opcjonalny filtr po użytkowniku -->
            <div class="col-md-3">
                <label for="user_id">Filter by User (optional)</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (isset($userId) && $userId == $user->id) ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Filtr po datach -->
            <div class="col-md-3">
                <label for="date_from">From (Issued At)</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label for="date_to">To (Issued At)</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <!-- Podsumowanie salda, tylko gdy wybrano użytkownika -->
    @if(isset($userId) && $balance !== null)
        @if($balance > 0)
            <div class="alert alert-success">
                Overpayment (Credit): {{ number_format($balance, 2) }} PLN
            </div>
        @elseif($balance < 0)
            <div class="alert alert-danger">
                Outstanding Payment: {{ number_format(abs($balance), 2) }} PLN
            </div>
        @else
            <div class="alert alert-info">Zero Balance.</div>
        @endif
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tabela dokumentów -->
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Document</th>
                <th>Client</th>
                <th>Amount Due</th>
                <th>Paid Amount</th>
                <th>Document Type</th>
                <th>Issued At</th>
                <th>Payment Method</th>
                <th>Billing Status</th>
                <th>Paid At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wzs as $billing)
                <tr>
                    <td>{{ $billing->id }}</td>
                    <td>{{ $billing->letter }}{{ $billing->number }}</td>
                    <td>{{ $billing->client_name }}</td>
                    <td>{{ $billing->amount }}</td>
                    <td>{{ $billing->paid_amount }}</td>
                    <td>{{ ucfirst($billing->document_type) }}</td>
                    <td>{{ $billing->issued_at }}</td>
                    <td>{{ $billing->payment_method }}</td>
                    <td>{{ $billing->billing_status }}</td>
                    <td>{{ $billing->paid_at }}</td>
                    <td>
                        <a href="{{ route('billing.show', $billing) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('billing.edit', $billing) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('payments.addPaymentForm', $billing) }}" class="btn btn-success btn-sm">Add Payment</a>
                        <form action="{{ route('billing.destroy', $billing) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
