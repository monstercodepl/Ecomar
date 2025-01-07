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

        return view('trucks/trucks', ['trucks' => $trucks]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trucks/new-truck');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $truck = new Truck; 
        $truck->registration = $request->registration;
        $truck->capacity = $request->capacity;
        $truck->vin = $request->vin;
        $truck->oc_date = $request->oc_date;
        $truck->oc_number = $request->oc_number;
        $truck->inspection_date = $request->inspection_date;
        $truck->amount = 0;
        $truck->save();

        $trucks = Truck::all();

        return redirect('trucks');
    }

    /**
     * Display the specified resource.
     */
    public function show(Truck $truck)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Truck $truck)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Truck $truck)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $truck = Truck::find($request->truck_id);
        $truck->delete();

        return redirect('trucks');
    }
}
