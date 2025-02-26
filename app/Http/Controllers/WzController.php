<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Wz;
use App\Models\User;
use App\Models\Address;
use App\Mail\JobFinished;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class WzController extends Controller
{

    public function index()
    {
        $wzs = Wz::all();

        return view('wz.index', ['wzs' => $wzs]);
    }

    public function show($id)
    {        
        $wz = Wz::find($id);
        
        return view('/wz.edit', ['wz' => $wz]);
    }

    public function update(Request $request)
    {
        $wz = Wz::find($request->id);

        $wz->amount = $request->amount;
        $wz->price = $request->price;
        $wz->save();

        return redirect('/wz');
    }

    public function create()
    {

        $addreses = Address::all();
        $users = User::all();

        return view('wz.create', ['addresses' => $addreses, 'users' => $users]);
    }

    public function save(Request $request)
    {
        $address = Address::find($request->address);
        $client = $address->user;

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

        return redirect('/wz-create');
    }
    /**
     *
     *
     * @param  int  $jobId
     * @return \Illuminate\Http\Response
     */

    public function send($id)
    {
        $wz = Wz::find($id);

        $client = User::find($wz->userId);

        $email = $client->email;

        if($client->secondary_email) {
            $email = $client->secondary_email;
        }

        if($client->default_email) {
            $email = 'wz_ecomar@op.pl';
        }

        $pdf = PDF::loadView('mail.job.finished_pdf', ['wz' => $wz]);

        $wzId = $wz->letter . $wz->number . '/' . $wz->month . '/' . $wz->year;
        $fileName = "WZ_" . str_replace(['/', ' '], '_', $wzId) . ".pdf";

        $message = new JobFinished($wz);
        $message->attachData($pdf->output(), $fileName);


        Mail::to($email)->send($message);

        $wz->sent = true;
        $wz->save();

        return back();
    }

    public function download($id)
    {
        $wz = Wz::find($id);

        $client = User::find($wz->userId);

        $email = $client->email;

        if($client->secondary_email) {
            $email = $client->secondary_email;
        }

        if($client->default_email) {
            $email = 'wz_ecomar@op.pl';
        }

        $pdf = PDF::loadView('mail.job.finished_pdf', ['wz' => $wz]);

        $wzId = $wz->letter . $wz->number . '/' . $wz->month . '/' . $wz->year;
        $fileName = "WZ_" . str_replace(['/', ' '], '_', $wzId) . ".pdf";

        return $pdf->download($fileName);
    }
}
