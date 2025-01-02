<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $municipalities = Municipality::all();

        return view('municipalities/municipalities', ['municipalities' => $municipalities]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('municipalities/new-municipality');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $municipality = new Municipality;
        $municipality->name = $request->name;
        $municipality->save();

        return redirect('municipalities');
    }

    /**
     * Display the specified resource.
     */
    public function show(Municipality $municipality)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Municipality $municipality)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Municipality $municipality)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Municipality $municipality)
    {
        $municipality = Municipality::find($request->municipality_id);
        $municipality->delete();

        return redirect('municipalities');
    }
}
