<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Job;
use App\Models\Truck;
use App\Models\Catchment;
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
        $catchments = Catchment::all();

        $truck_jobs = [];
        if($truck->job_1){
            $truck_job = $jobs->find($truck->job_1);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_2){
            $truck_job = $jobs->find($truck->job_2);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_3){
            $truck_job = $jobs->find($truck->job_3);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_4){
            $truck_job = $jobs->find($truck->job_4);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_5){
            $truck_job = $jobs->find($truck->job_5);
            array_push($truck_jobs, $truck_job);
        }


        return view('work/work', ['jobs' => $jobs, 'truck' => $truck, 'catchments' => $catchments, 'truck_jobs' => $truck_jobs]);
    }

    public function pump(Request $request)
    {  
        $user = Auth::user();

        $truck = $user->truck;
        $truck->amount = $truck->amount + $request->amount;
        if($truck->job_1 === null) $truck->job_1 = $request->job_id;
        else if($truck->job_2 === null) $truck->job_2 = $request->job_id;
        else if($truck->job_3 === null) $truck->job_3 = $request->job_id;
        else if($truck->job_4 === null) $truck->job_4 = $request->job_id;
        else if($truck->job_5 === null) $truck->job_5 = $request->job_id;
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

    public function status(Request $request)
    {
        $job = Job::find($request->job_id);
        $job->status = $request->status;
        $job->save();

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
