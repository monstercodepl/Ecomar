<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $zones = Zone::all();

        return view('zones/zones', ['zones' => $zones]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('zones/new-zone');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $zone = new Zone;
        $zone->name = $request->name;
        $zone->price = $request->price;
        $zone->save();

        return redirect('zones');
    }

    /**
     * Display the specified resource.
     */
    public function show(Zone $zone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Zone $zone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zone $zone)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $zone = Zone::find($request->zone_id);
        $zone->delete();

        return redirect('zones');
    }
}
