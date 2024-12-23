<?php

namespace App\Http\Controllers;

use App\Models\Address;
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
        return view('address/new-address');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $address = new Address;
        $address->numer = $request->numer;
        $address->adres = $request->ulica;
        $address->gmina = $request->gmina;
        $address->miasto = $request->miasto;
        $address->aglomeracja = $request->has('aglomeracja');
        $address->zbiornik = $request->zbiornik;
        $address->save();

        $addresses = Address::all();

        return view('address/addresses', ['addresses' => $addresses]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $address = Address::find($id);

        return view('address/address', ['address' => $address]);
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
        $address->gmina = $request->gmina;
        $address->miasto = $request->miasto;
        $address->aglomeracja = $request->has('aglomeracja');
        $address->zbiornik = $request->zbiornik;
        $address->save();

        $addresses = Address::all();

        return view('address/addresses', ['addresses' => $addresses]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        //
    }
}
