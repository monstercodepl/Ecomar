<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $addresses = Address::all();
        $users = User::all();

        return view('jobs/new-job', ['addresses' => $addresses, 'users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $job = new Job;
        $job->user_id = $request->user;
        $job->address_id = $request->address;
        $job->status = 'Nowe';
        $job->schedule = $request->date;
        $job->save();

        $jobs = Job::all();

        return view('jobs/jobs', ['jobs' => $jobs]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        //
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
    public function update(Request $request, Job $job)
    {
        //
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
