@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Add Payment to Document</h1>
    <p>
        <strong>Document:</strong> {{ $wz->letter }}{{ $wz->number }}<br>
        <strong>Client:</strong> {{ $wz->client_name }}<br>
        <strong>Amount Due:</strong> {{ $wz->amount }}<br>
        <strong>Already Paid:</strong> {{ $wz->paid_amount }}
    </p>
    <form action="{{ route('payments.store', $wz) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="payment_amount">Payment Amount</label>
            <input type="number" step="0.01" name="payment_amount" id="payment_amount" class="form-control" required>
            @error('payment_amount')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mt-2">
            <label for="payment_method">Payment Method (optional)</label>
            <input type="text" name="payment_method" id="payment_method" class="form-control">
            @error('payment_method')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary mt-3">Add Payment</button>
    </form>
</div>
@endsection
