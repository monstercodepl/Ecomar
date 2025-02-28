<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Wz;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Wyświetla formularz dodawania dokumentu płatności.
     */
    public function create()
    {
        // Pobieramy listę WZ, dla których można dodać dokument (np. wszystkie lub tylko te, które nie są opłacone gotówką)
        $wzs = Wz::orderBy('created_at', 'desc')->get();
        $users = User::all();
        return view('payments.create', compact('wzs', 'users'));
    }

    /**
     * Zapisuje nowy dokument płatności.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Pole "number" usunięte – numer płatności będzie generowany automatycznie (np. przez autoinkrementację)
            'wz_id'         => 'nullable|exists:wzs,id', // opcjonalnie powiązany dokument WZ
            'user_id'       => 'required|exists:users,id',
            'amount'        => 'required|numeric',
            'payment_date'  => 'required|date',
            'method'        => 'required|string|max:50',
            'status'        => 'required|in:pending,confirmed,rejected',
            // Jeśli chcesz obsługiwać plik, odkomentuj poniższe:
            // 'document'      => 'nullable|file|mimes:pdf,jpg,png',
        ]);

        // Obsługa pliku (jeśli został przesłany)
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('payments', 'public');
            $validatedData['document'] = $path;
        }

        // Tworzymy płatność; pole "number" nie jest już pobierane z formularza
        Payment::create($validatedData);

        // Jeśli metoda płatności to gotówka, możemy ustawić flagę cash na true dla powiązanego dokumentu WZ.
        if ($request->wz_id && $request->method === 'gotówka') {
            $wz = Wz::find($request->wz_id);
            if ($wz) {
                $wz->cash = true;
                $wz->save();
            }
        }

        return redirect()->route('billings.index')->with('success', 'Dokument płatności został dodany.');
    }
}
