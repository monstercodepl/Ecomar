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
use Illuminate\Support\Facades\Log;

class WzController extends Controller
{
    public function index()
    {
        $wzs = Wz::all();
        return view('wz.index', compact('wzs'));
    }

    public function show(Wz $wz)
    {        
        return view('wz.edit', compact('wz'));
    }

    public function update(Request $request, Wz $wz)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'price'  => 'required|numeric|min:0',
        ]);

        $wz->amount = $validated['amount'];
        $wz->price = $validated['price'];
        $wz->save();

        return redirect()->route('wzs');
    }

    public function create()
    {
        $addresses = Address::all();
        $users = User::all();
        return view('wz.create', compact('addresses', 'users'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|exists:addresses,id',
            'letter'  => 'required|string|max:10',
            'month'   => 'required|string|max:2',
            'year'    => 'required|string|max:4',
            'price'   => 'required|numeric|min:0',
            'amount'  => 'required|numeric|min:0',
        ]);

        $address = Address::findOrFail($validated['address']);
        $client = $address->user;

        $wzsCount = Wz::where('letter', $validated['letter'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->count();
        $wzNumber = $wzsCount + 1;

        $wz = new Wz;
        $wz->number = $wzNumber;
        $wz->letter = $validated['letter'];
        $wz->month = $validated['month'];
        $wz->year = $validated['year'];
        $wz->client_name = $client->name;
        $wz->userId = $client->id;
        $wz->client_address = trim(($address->adres ?? '') . ' ' . ($address->numer ?? '') . ', ' . ($address->miasto ?? ''));
        $wz->addressId = $address->id;
        $wz->price = $validated['price'];
        $wz->amount = $validated['amount'];
        $wz->save();

        return redirect()->route('create-wz');
    }

    public function send(Wz $wz)
    {
        $client = User::findOrFail($wz->userId);
        $email = $this->determineClientEmail($client);

        $pdf = Pdf::loadView('mail.job.finished_pdf', ['wz' => $wz]);
        $fileName = $this->generatePdfFileName($wz);

        $message = new JobFinished($wz);
        $message->attachData($pdf->output(), $fileName);

        try {
            Mail::to($email)->send($message);
            $wz->sent = true;
            $wz->save();
        } catch (\Exception $e) {
            Log::error('Error sending WZ email: ' . $e->getMessage());
            // Opcjonalnie: przekazać komunikat o błędzie do widoku
        }

        return back();
    }

    public function download(Wz $wz)
    {
        $pdf = Pdf::loadView('mail.job.finished_pdf', ['wz' => $wz]);
        $fileName = $this->generatePdfFileName($wz);
        return $pdf->download($fileName);
    }

    // Prywatne metody pomocnicze

    private function generateWzId(Wz $wz): string
    {
        return $wz->letter . $wz->number . '/' . $wz->month . '/' . $wz->year;
    }

    private function generatePdfFileName(Wz $wz): string
    {
        $wzId = $this->generateWzId($wz);
        return "WZ_" . str_replace(['/', ' '], '_', $wzId) . ".pdf";
    }

    private function determineClientEmail(User $client): string
    {
        $email = $client->email;
        if ($client->secondary_email) {
            $email = $client->secondary_email;
        }
        if ($client->default_email) {
            $email = 'wz_ecomar@op.pl';
        }
        return $email;
    }
}
