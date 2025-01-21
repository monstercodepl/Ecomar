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

        return view('address/addresses', ['addresses' => $addresses]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::all();
        $municipalities = Municipality::all();
        $users = User::all();
        
        return view('address/new-address', ['zones' => $zones, 'municipalities' => $municipalities, 'users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $address = new Address;
        $address->numer = $request->numer;
        $address->adres = $request->ulica;
        $address->municipality_id = $request->municipality;
        $address->miasto = $request->miasto;
        $address->aglomeracja = $request->has('aglomeracja');
        $address->zbiornik = $request->zbiornik;
        $address->zone_id = $request->zone_id;
        $address->user_id = $request->user_id;
        $address->save();

        $addresses = Address::all();

        return redirect('addresses');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $address = Address::find($id);
        $zones = Zone::all();
        $municipalities = Municipality::all();
        $users = User::all();

        return view('address/address', ['address' => $address, 'zones' => $zones, 'municipalities' => $municipalities, 'users' => $users]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $address = Address::find($request->id);
        $address->numer = $request->numer;
        $address->adres = $request->ulica;
        $address->municipality_id = $request->municipality;
        $address->miasto = $request->miasto;
        $address->aglomeracja = $request->has('aglomeracja');
        $address->zbiornik = $request->zbiornik;
        $address->zone_id = $request->zone_id;
        $address->user_id = $request->user_id;
        $address->save();

        $addresses = Address::all();

        return redirect('addresses');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $address = Address::find($request->address_id);
        $address->delete();

        return redirect('addresses');
    }
}
