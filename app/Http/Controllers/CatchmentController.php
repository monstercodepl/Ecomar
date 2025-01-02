<?php

namespace App\Http\Controllers;

use App\Models\Catchment;
use Illuminate\Http\Request;

class CatchmentController extends Controller
{
    public function index()
    {
        $catchments = Catchment::all();

        return view('catchments/catchments', ['catchments' => $catchments]);
    }

    public function create()
    {
        return view('catchments/new-catchment');
    }

    public function store(Request $request)
    {
        $catchment = new Catchment; 
        $catchment->name = $request->name;
        $catchment->save();

        return redirect('/catchments');
    }

    public function destroy(Request $request)
    {
        $catchment = Catchment::find($request->catchment_id);
        $catchment->delete();

        return redirect('/catchments');
    }
}