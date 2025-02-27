@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Create Document</h1>
    <form action="{{ route('billing.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="letter">Letter</label>
            <input type="text" name="letter" id="letter" class="form-control" value="{{ old('letter') }}" required>
            @error('letter') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="number">Number</label>
            <input type="number" name="number" id="number" class="form-control" value="{{ old('number') }}" required>
            @error('number') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="month">Month</label>
            <input type="text" name="month" id="month" class="form-control" value="{{ old('month') }}" required>
            @error('month') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="year">Year</label>
            <input type="text" name="year" id="year" class="form-control" value="{{ old('year') }}" required>
            @error('year') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="client_name">Client Name</label>
            <input type="text" name="client_name" id="client_name" class="form-control" value="{{ old('client_name') }}" required>
            @error('client_name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="userId">Client (User ID)</label>
            <input type="number" name="userId" id="userId" class="form-control" value="{{ old('userId') }}" required>
            @error('userId') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="client_address">Client Address</label>
            <input type="text" name="client_address" id="client_address" class="form-control" value="{{ old('client_address') }}">
            @error('client_address') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="price">Price</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
            @error('price') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="amount">Amount Due</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
            @error('amount') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <!-- Billing fields -->
        <div class="form-group mt-2">
            <label for="paid_amount">Paid Amount</label>
            <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control" value="{{ old('paid_amount', 0) }}" required>
            @error('paid_amount') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="document_type">Document Type</label>
            <select name="document_type" id="document_type" class="form-control" required>
                <option value="wz" {{ old('document_type') == 'wz' ? 'selected' : '' }}>WZ</option>
                <option value="invoice" {{ old('document_type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                <option value="pk" {{ old('document_type') == 'pk' ? 'selected' : '' }}>PK</option>
            </select>
            @error('document_type') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <!-- Pokazujemy pole previous_year_balance tylko dla PK -->
        <div class="form-group mt-2" id="pk_balance_field" style="display: {{ old('document_type') == 'pk' ? 'block' : 'none' }};">
            <label for="previous_year_balance">Previous Year Balance</label>
            <input type="number" step="0.01" name="previous_year_balance" id="previous_year_balance" class="form-control" value="{{ old('previous_year_balance') }}">
            @error('previous_year_balance') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="issued_at">Issued At</label>
            <input type="date" name="issued_at" id="issued_at" class="form-control" value="{{ old('issued_at') }}">
            @error('issued_at') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="payment_method">Payment Method</label>
            <input type="text" name="payment_method" id="payment_method" class="form-control" value="{{ old('payment_method') }}">
            @error('payment_method') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="billing_status">Billing Status</label>
            <select name="billing_status" id="billing_status" class="form-control" required>
                <option value="pending" {{ old('billing_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ old('billing_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ old('billing_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
            @error('billing_status') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="paid_at">Paid At</label>
            <input type="datetime-local" name="paid_at" id="paid_at" class="form-control" value="{{ old('paid_at') }}">
            @error('paid_at') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mt-2">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
            @error('notes') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary mt-3">Create Document</button>
    </form>

    <script>
        // Pokazuje lub ukrywa pole previous_year_balance w zależności od wybranego typu dokumentu
        document.getElementById('document_type').addEventListener('change', function() {
            var pkField = document.getElementById('pk_balance_field');
            if(this.value === 'pk') {
                pkField.style.display = 'block';
            } else {
                pkField.style.display = 'none';
            }
        });
    </script>
</div>
@endsection
