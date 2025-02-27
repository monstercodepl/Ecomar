<?php

namespace App\Http\Controllers;

use App\Models\Wz;
use App\Models\User;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $userId   = $request->get('user_id'); // opcjonalny filtr
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $query = Wz::query();

        if ($userId) {
            $query->where('userId', $userId);
        }

        if ($dateFrom) {
            $query->whereDate('issued_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('issued_at', '<=', $dateTo);
        }

        $wzs = $query->get();
        $users = User::all();

        // Oblicz saldo tylko, gdy wybrano użytkownika
        $balance = null;
        if ($userId) {
            $balance = $wzs->sum(function ($doc) {
                if ($doc->document_type === 'pk') {
                    return $doc->previous_year_balance ?? 0;
                }
                return $doc->paid_amount - $doc->amount;
            });
        }

        return view('billing.index', compact('wzs', 'users', 'userId', 'balance', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        return view('billing.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'letter'         => 'required|string|max:10',
            'number'         => 'required|integer',
            'month'          => 'required|string|max:2',
            'year'           => 'required|string|max:4',
            'client_name'    => 'required|string|max:255',
            'userId'         => 'required|exists:users,id',
            'client_address' => 'nullable|string|max:255',
            'price'          => 'required|numeric|min:0',
            'amount'         => 'required|numeric|min:0',
            // Billing fields:
            'paid_amount'    => 'nullable|numeric|min:0',
            'billing_status' => 'required|string|in:pending,paid,overdue',
            'payment_method' => 'nullable|string|max:100',
            'issued_at'      => 'nullable|date',
            'paid_at'        => 'nullable|date',
            'document_type'  => 'required|string|in:wz,invoice,pk',
            'previous_year_balance' => 'nullable|numeric',
        ]);

        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;
        $validated['billing_status'] = $validated['billing_status'] ?? 'pending';
        $validated['issued_at'] = $validated['issued_at'] ?? now()->toDateString();
        if ($validated['document_type'] !== 'pk') {
            $validated['previous_year_balance'] = null;
        }

        Wz::create($validated);

        return redirect()->route('billing.index')->with('success', 'Document created successfully.');
    }

    public function show(Wz $billing)
    {
        return view('billing.show', compact('billing'));
    }

    public function edit(Wz $billing)
    {
        $users = User::all();
        return view('billing.edit', compact('billing', 'users'));
    }

    public function update(Request $request, Wz $billing)
    {
        $validated = $request->validate([
            'letter'         => 'required|string|max:10',
            'number'         => 'required|integer',
            'month'          => 'required|string|max:2',
            'year'           => 'required|string|max:4',
            'client_name'    => 'required|string|max:255',
            'userId'         => 'required|exists:users,id',
            'client_address' => 'nullable|string|max:255',
            'price'          => 'required|numeric|min:0',
            'amount'         => 'required|numeric|min:0',
            'paid_amount'    => 'required|numeric|min:0',
            'billing_status' => 'required|string|in:pending,paid,overdue',
            'payment_method' => 'nullable|string|max:100',
            'issued_at'      => 'nullable|date',
            'paid_at'        => 'nullable|date',
            'document_type'  => 'required|string|in:wz,invoice,pk',
            'previous_year_balance' => 'nullable|numeric',
        ]);

        if ($validated['document_type'] !== 'pk') {
            $validated['previous_year_balance'] = null;
        }

        $billing->update($validated);
        return redirect()->route('billing.index')->with('success', 'Document updated successfully.');
    }

    public function destroy(Wz $billing)
    {
        $billing->delete();
        return redirect()->route('billing.index')->with('success', 'Document deleted successfully.');
    }
}
