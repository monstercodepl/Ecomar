<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::all();
        return view('jobs.jobs', compact('jobs'));
    }

    public function daily()
    {
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs.daily', compact('drivers'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $jobs = Job::when($request->driver_id, function ($query, $driver_id) {
                    $query->where('driver_id', $driver_id);
                })
                ->whereDate('schedule', $request->date)
                ->where('status', 'Nowe')
                ->orderBy('updated_at', 'asc')
                ->get();

        return view('jobs.report', ['jobs' => $jobs, 'date' => $request->date]);
    }

    public function done_report()
    {
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs.done_report', compact('drivers'));
    }

    public function generate_done_report(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $jobs = Job::when($request->driver_id, function ($query, $driver_id) {
                    $query->where('driver_id', $driver_id);
                })
                ->whereDate('schedule', $request->date)
                ->orderBy('updated_at', 'asc')
                ->get();

        return view('jobs.report_done', ['jobs' => $jobs, 'date' => $request->date]);
    }

    public function index_client()
    {
        $user = Auth::user();
        $jobs = Job::where('user_id', $user->id)->get();
        return view('jobs.my_jobs', compact('jobs'));
    }

    public function create()
    {
        $addresses = Address::all();
        $users = User::all();
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs.new-job', compact('addresses', 'users', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required',
            'date'    => 'required|date',
            'driver'  => 'required',
        ]);

        $job = new Job;
        $job->address_id = $request->address;
        $job->status     = 'Nowe';
        $job->schedule   = $request->date;
        $job->driver_id  = $request->driver;
        $job->comment    = $request->comment;
        $job->save();

        return redirect()->route('jobs');
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs.job', compact('job', 'drivers'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:jobs,id',
            'date'   => 'required|date',
            'driver' => 'required',
        ]);

        $job = Job::findOrFail($request->id);
        $job->schedule  = $request->date;
        $job->driver_id = $request->driver;
        $job->comment   = $request->comment;
        $job->save();

        return redirect()->route('jobs');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id'
        ]);

        $job = Job::findOrFail($request->job_id);
        $job->delete();

        return redirect()->route('jobs');
    }
}
