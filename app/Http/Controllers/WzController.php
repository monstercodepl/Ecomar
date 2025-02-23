<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Wz;
use App\Models\User;
use App\Models\Address;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WzController extends Controller
{

    public function index()
    {
        $wzs = Wz::all();

        return view('wz.index', ['wzs' => $wzs]);
    }

    public function create()
    {

        $addreses = Address::all();
        $users = User::all();

        return view('wz.create', ['addresses' => $addreses, 'users' => $users]);
    }

    public function save(Request $request)
    {
        $client = User::find($request->client);
        $address = Address::find($request->address);

        $wzs = Wz::where('letter', $request->letter)->where('month', $request->month)->where('year', $request->year)->get()->count();
        $wzNumber = $wzs + 1;

        $wz = new Wz;
        $wz->number = $wzNumber;
        $wz->letter = $request->letter;
        $wz->month = $request->month;
        $wz->year = $request->year;
        $wz->client_name = $client->name;
        $wz->userId = $client->id;
        $wz->client_address = ($address->adres ?? '').' '.($address->numer ?? '').', '.($address->miasto ?? '');
        $wz->addressId = $address->id;
        $wz->price = $request->price;
        $wz->amount = $request->amount;
        $wz->save();

        return back();
    }
    /**
     *
     *
     * @param  int  $jobId
     * @return \Illuminate\Http\Response
     */
    public function download($jobId)
    {
        $job = Job::with(['address.zone', 'address.user'])->findOrFail($jobId);
        if(!is_null($job->partial)) {
            $originalJob = Job::find($job->partial);
            $job->pumped = $job->pumped + $originalJob->pumped;
            $job->price = $job->price + $originalJob->price;    
        }

        $wz = Wz::where('job_id', $jobId)->firstOrFail();

        $wzId = $wz->letter . $wz->number . '/' . $wz->month . '/' . $wz->year;

        $pdf = Pdf::loadView('mail.job.finished_pdf', [
            'job' => $job,
            'id'  => $wzId,
        ]);

        $fileName = "WZ_" . str_replace(['/', ' '], '_', $wzId) . ".pdf";

        return $pdf->download($fileName);
    }
}
