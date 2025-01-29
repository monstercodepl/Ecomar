<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::all();

        return view('jobs/jobs', ['jobs' => $jobs]);
    }

    public function index_client()
    {
        $user = Auth::user();

        $jobs = Job::where('user_id', $user->id)->get();

        return view('jobs/my_jobs', ['jobs' => $jobs]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $addresses = Address::all();
        $users = User::all();
        $drivers = User::whereNotNull('truck_id')->get();

        return view('jobs/new-job', ['addresses' => $addresses, 'users' => $users, 'drivers' => $drivers]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $job = new Job;
        $job->address_id = $request->address;
        $job->status = 'Nowe';
        $job->schedule = $request->date;
        $job->driver_id = $request->driver;
        $job->save();

        $jobs = Job::all();

        return view('jobs/jobs', ['jobs' => $jobs]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $job = Job::find($id);
        
        return view('jobs/job', ['job' => $job]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $job = Job::find($request->id);
        $job->schedule = $request->date;
        $job->save();

        return redirect('jobs');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $job = Job::find($request->job_id);
        $job->delete();

        return redirect('jobs');
    }
}
