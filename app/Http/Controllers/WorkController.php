<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Job;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkController extends Controller
{
    public function jobs()
    {
        // has to be filtered by driver
        $jobs = Job::all();
        $user = Auth::user();
        $truck = $user->truck;


        return view('work/work', ['jobs' => $jobs, 'truck' => $truck]);
    }

    public function pump(Request $request)
    {  
        $user = Auth::user();

        $truck = $user->truck;
        $truck->amount = $truck->amount + $request->amount;
        $truck->save();

        $job = Job::find($request->job_id);
        $job->pumped = $job->pumped + $request->amount;
        $job->save();

        $work = new Work;
        $work->job_id = $request->job_id;
        $work->amount = $request->amount;
        $work->user_id = $user->id;
        $work->truck_id = $truck->id;
        $work->save();



        return redirect('work');
    }

    public function dump(Request $request)
    {
        $user = Auth::user();

        $work = new Work;
        $work->amount = $request->amount;
        $work->truck_id = $request->truck_id;
        $work->user_id = $user->id;
        $work->type = 'dump';
        $work->save();

        $truck = $user->truck;
        $truck->amount = $truck->amount - $request->amount;
        $truck->save();

        return redirect('work');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Work $work)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Work $work)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Work $work)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Work $work)
    {
        //
    }
}
