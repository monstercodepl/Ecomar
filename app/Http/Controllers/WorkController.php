<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Job;
use App\Models\Truck;
use App\Models\Catchment;
use App\Mail\JobFinished;
use App\Models\Wz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;


class WorkController extends Controller
{
    public function select()
    {
        $drivers = User::whereNotNull('truck_id')->get();

        return view('work/select', ['drivers' => $drivers]);
    }
    public function jobs()
    {
        // has to be filtered by driver
        $jobs = Job::where('status', 'Nowe')->get();
        $user = Auth::user();
        $truck = $user->truck;
        $catchments = Catchment::all();

        $current_jobs = Job::where('status', 'pumped')->where('truck_id', $truck->id)->get();

        $truck_jobs = [];
        if($truck->job_1){
            $truck_job = Job::find($truck->job_1);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_2){
            $truck_job = Job::find($truck->job_2);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_3){
            $truck_job = Job::find($truck->job_3);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_4){
            $truck_job = Job::find($truck->job_4);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_5){
            $truck_job = Job::find($truck->job_5);
            array_push($truck_jobs, $truck_job);
        }


        return view('work/work', ['jobs' => $jobs, 'truck' => $truck, 'catchments' => $catchments, 'truck_jobs' => $truck_jobs, 'current_jobs' => $current_jobs]);
    }

    public function jobs_select(Request $request)
    {
          // has to be filtered by driver
          $jobs = Job::where('status', 'Nowe')->get();
          $user = User::find($request->driver_id);
          $truck = $user->truck;
          $catchments = Catchment::all();
  
          $current_jobs = Job::where('status', 'pumped')->where('truck_id', $truck->id)->get();
  
          $truck_jobs = [];
          if($truck->job_1){
              $truck_job = Job::find($truck->job_1);
              array_push($truck_jobs, $truck_job);
          }
          if($truck->job_2){
              $truck_job = Job::find($truck->job_2);
              array_push($truck_jobs, $truck_job);
          }
          if($truck->job_3){
              $truck_job = Job::find($truck->job_3);
              array_push($truck_jobs, $truck_job);
          }
          if($truck->job_4){
              $truck_job = Job::find($truck->job_4);
              array_push($truck_jobs, $truck_job);
          }
          if($truck->job_5){
              $truck_job = Job::find($truck->job_5);
              array_push($truck_jobs, $truck_job);
          }
  
  
          return view('work/work', ['jobs' => $jobs, 'truck' => $truck, 'catchments' => $catchments, 'truck_jobs' => $truck_jobs, 'current_jobs' => $current_jobs, 'user' => $user]);
      
    }

    public function pump(Request $request)
    {  
        $user = Auth::user();

        if($request->has('user'))
        {
            $user = User::find($request->user);
        }

        dump($user);
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
        $job->status = 'pumped';
        $job->truck_id = $truck->id;
        $job->cash = $request->has('cash');
        $job->save();

        $job->price = $job->pumped * $job->address->zone->price;
        $job->save();

        $work = new Work;
        $work->job_id = $request->job_id;
        $work->amount = $request->amount;
        $work->user_id = $user->id;
        $work->truck_id = $truck->id;
        $work->save();

        $client = $job->address->user;

        $email = $client->email;

        if(is_null($client->nip)){
            if($client->secondary_email) {
                $email = $client->secondary_email;
            }

            if($client->default_email) {
                $email = 'wz_ecomar@op.pl';
            }

            $letter = $user->letter;
            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');

            $wzs = Wz::where('letter', $letter)->where('month', $month)->where('year', $year)->get()->count();
            $number = $wzs + 1;

            $wzId = $letter.$number.'/'.$month.'/'.$year;

            $wz = new Wz;
            $wz->number = $number;
            $wz->month = $month;
            $wz->year = $year;
            $wz->letter = $letter;
            $wz->job_id = $job->id;
            $wz->save();

            $pdf = PDF::loadView('mail.job.finished_pdf', ['job' => $job, 'id' => $wzId]);
            $message = new JobFinished($job);
            $message->attachData($pdf->output(), "wz.pdf");

            Mail::to($email)->send($message);
        }

        return Redirect::back();
    }

    public function dump(Request $request)
    {
        $user = Auth::user();

        if($request->has('user')){
            $user = User::find($request->user);
        }

        dump($user);

        $work = new Work;
        $work->amount = $request->amount;
        $work->truck_id = $request->truck_id;
        $work->user_id = $user->id;
        $work->type = 'dump';
        $work->save();

        $truck = $user->truck;

        $truck_jobs = [];
        if($truck->job_1){
            $truck_job = Job::find($truck->job_1);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_2){
            $truck_job = Job::find($truck->job_2);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_3){
            $truck_job = Job::find($truck->job_3);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_4){
            $truck_job = Job::find($truck->job_4);
            array_push($truck_jobs, $truck_job);
        }
        if($truck->job_5){
            $truck_job = Job::find($truck->job_5);
            array_push($truck_jobs, $truck_job);
        }

        $difference = $truck->amount - $request->amount;
        $difference_part = $difference / $truck->amount;

        foreach($truck_jobs as $truck_job)
        {
            $truck_job->corrected = $truck_job->pumped - $truck_job->pumped * $difference_part;
            $truck_job->status = 'done';
            $truck_job->catchment_id = $request->catchment_id;
            $truck_job->save();
        }

        $truck->amount = 0;
        $truck->job_1 = null;
        $truck->job_2 = null;
        $truck->job_3 = null;
        $truck->job_4 = null;
        $truck->job_5 = null;
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
