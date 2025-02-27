<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Zone;
use App\Models\Municipality;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::all();
        return view('address.addresses', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::all();
        $municipalities = Municipality::all();
        $users = User::all();
        
        return view('address.new-address', compact('zones', 'municipalities', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numer'         => 'required|string|max:50',
            'ulica'         => 'required|string|max:255',
            'miasto'        => 'required|string|max:255',
            'municipality'  => 'required|exists:municipalities,id',
            'zone_id'       => 'required|exists:zones,id',
            'zbiornik'      => 'nullable|string|max:255',
            'user_id'       => 'nullable|exists:users,id',
            'aglomeracja'   => 'sometimes',
        ]);

        $exists = Address::where('numer', $validated['numer'])
            ->where('adres', $validated['ulica'])
            ->where('miasto', $validated['miasto'])
            ->exists();

        if ($exists) {
            return redirect()->route('addresses');
        }

        $address = new Address;
        $address->numer = $validated['numer'];
        $address->adres = $validated['ulica'];
        $address->municipality_id = $validated['municipality'];
        $address->miasto = $validated['miasto'];
        $address->aglomeracja = $request->has('aglomeracja');
        $address->zbiornik = $validated['zbiornik'] ?? null;
        $address->zone_id = $validated['zone_id'];
        $address->user_id = $validated['user_id'] ?? null;
        $address->save();

        return redirect()->route('addresses');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $address = Address::findOrFail($id);
        $zones = Zone::all();
        $municipalities = Municipality::all();
        $users = User::all();

        return view('address.address', compact('address', 'zones', 'municipalities', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        // Metoda pusta, formularz edycji jest wyświetlany w metodzie show.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'            => 'required|exists:addresses,id',
            'numer'         => 'required|string|max:50',
            'ulica'         => 'required|string|max:255',
            'miasto'        => 'required|string|max:255',
            'municipality'  => 'required|exists:municipalities,id',
            'zone_id'       => 'required|exists:zones,id',
            'zbiornik'      => 'nullable|string|max:255',
            'user_id'       => 'nullable|exists:users,id',
            'aglomeracja'   => 'sometimes',
        ]);

        $address = Address::findOrFail($validated['id']);
        $address->numer = $validated['numer'];
        $address->adres = $validated['ulica'];
        $address->municipality_id = $validated['municipality'];
        $address->miasto = $validated['miasto'];
        $address->aglomeracja = $request->has('aglomeracja');
        $address->zbiornik = $validated['zbiornik'] ?? null;
        $address->zone_id = $validated['zone_id'];
        $address->user_id = $validated['user_id'] ?? null;
        $address->save();

        return redirect()->route('addresses');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $address = Address::findOrFail($request->address_id);
        $address->delete();

        return redirect()->route('addresses');
    }
}
