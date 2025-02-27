@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Document Details</h1>
    <p><strong>ID:</strong> {{ $billing->id }}</p>
    <p><strong>Document:</strong> {{ $billing->letter }}{{ $billing->number }}</p>
    <p><strong>Client:</strong> {{ $billing->client_name }}</p>
    <p><strong>Amount Due:</strong> {{ $billing->amount }}</p>
    <p><strong>Paid Amount:</strong> {{ $billing->paid_amount }}</p>
    @if($billing->document_type === 'pk')
        <p><strong>Previous Year Balance:</strong> {{ $billing->previous_year_balance }}</p>
    @endif
    <p><strong>Issued At:</strong> {{ $billing->issued_at }}</p>
    <p><strong>Payment Method:</strong> {{ $billing->payment_method }}</p>
    <p><strong>Billing Status:</strong> {{ $billing->billing_status }}</p>
    <p><strong>Paid At:</strong> {{ $billing->paid_at }}</p>
    <p><strong>Notes:</strong> {{ $billing->notes }}</p>
    <a href="{{ route('billing.index') }}" class="btn btn-secondary">Back to Document List</a>
</div>
@endsection
