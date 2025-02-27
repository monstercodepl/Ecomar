<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Wz;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Formularz dodania płatności dla danego dokumentu
    public function addPaymentForm(Wz $wz)
    {
        return view('billing.add_payment', compact('wz'));
    }

    // Przetwarzanie dodania płatności
    public function store(Request $request, Wz $wz)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:100',
        ]);

        $validated['paid_at'] = now();

        Payment::create([
            'wz_id'          => $wz->id,
            'amount'         => $validated['payment_amount'],
            'payment_method' => $validated['payment_method'],
            'paid_at'        => $validated['paid_at'],
        ]);

        // Aktualizujemy dokument: dodajemy płatność do pola paid_amount
        $wz->paid_amount += $validated['payment_amount'];
        if ($wz->paid_amount >= $wz->amount) {
            $wz->billing_status = 'paid';
        } else {
            $wz->billing_status = 'pending';
        }
        $wz->save();

        return redirect()->route('billing.index')->with('success', 'Payment added successfully.');
    }
}
