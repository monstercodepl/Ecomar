<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trucks = Truck::all();
        return view('trucks.trucks', compact('trucks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trucks.new-truck');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Walidacja danych wejściowych
        $validated = $request->validate([
            'registration'    => 'required|string|max:255',
            'capacity'        => 'required|numeric|min:0',
            'vin'             => 'required|string|max:255',
            'oc_date'         => 'required|date',
            'oc_number'       => 'required|string|max:255',
            'inspection_date' => 'required|date',
        ]);

        $truck = new Truck;
        $truck->registration    = $validated['registration'];
        $truck->capacity        = $validated['capacity'];
        $truck->vin             = $validated['vin'];
        $truck->oc_date         = $validated['oc_date'];
        $truck->oc_number       = $validated['oc_number'];
        $truck->inspection_date = $validated['inspection_date'];
        $truck->amount          = 0;
        $truck->save();

        return redirect()->route('trucks');
    }

    /**
     * Display the specified resource.
     */
    public function show(Truck $truck)
    {
        // Możesz zaimplementować wyświetlanie szczegółów pojazdu, jeżeli będzie potrzebne.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Truck $truck)
    {
        // Możesz zaimplementować formularz edycji, jeśli zajdzie taka potrzeba.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Truck $truck)
    {
        // Aktualizacja pojazdu – do implementacji według potrzeb.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $truck = Truck::findOrFail($request->truck_id);
        $truck->delete();

        return redirect()->route('trucks');
    }
}
