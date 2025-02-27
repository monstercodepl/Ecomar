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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class WorkController extends Controller
{
    /**
     * Zapobiega powtórzeniu tego samego requestu.
     *
     * @param Request $request
     * @param string $actionKey Klucz unikalny dla danej akcji.
     * @param int $seconds Czas blokady w sekundach.
     * @return void
     */
    private function preventDuplicateRequest(Request $request, string $actionKey, int $seconds = 5): void
    {
        $userId = Auth::id() ?? 'guest';
        $hash = md5($request->fullUrl() . serialize($request->all()));
        $key = "duplicate:{$actionKey}:{$userId}:{$hash}";

        if (cache()->has($key)) {
            abort(429, 'Duplicate request detected');
        }
        cache()->put($key, true, $seconds);
    }

    /**
     * Pobiera przypisane zlecenia dla ciężarówki.
     *
     * @param Truck $truck
     * @return array
     */
    private function getTruckJobs(Truck $truck): array
    {
        $jobIds = [];
        foreach (range(1, 5) as $i) {
            $jobField = 'job_' . $i;
            if ($truck->$jobField) {
                $jobIds[] = $truck->$jobField;
            }
        }
        return Job::whereIn('id', $jobIds)->get()->all();
    }

    public function select()
    {
        $drivers = User::whereNotNull('truck_id')->get();
        return view('work.select', compact('drivers'));
    }

    public function jobs()
    {
        $user = Auth::user();

        $jobs = Job::where('status', 'Nowe')
            ->where('driver_id', $user->id)
            ->whereDate('schedule', Carbon::now()->toDateString())
            ->get();

        $truck = $user->truck;
        $catchments = Catchment::all();
        $drivers = User::whereNotNull('truck_id')->get();
        $current_jobs = Job::where('status', 'pumped')
            ->where('truck_id', $truck->id)
            ->get();

        $truck_jobs = $this->getTruckJobs($truck);

        return view('work.work', compact('jobs', 'truck', 'catchments', 'truck_jobs', 'current_jobs', 'drivers'));
    }

    public function jobs_select($id)
    {
        $jobs = Job::where('status', 'Nowe')
            ->where('driver_id', $id)
            ->get();

        $user = User::findOrFail($id);
        $truck = $user->truck;
        $catchments = Catchment::all();
        $drivers = User::whereNotNull('truck_id')->get();
        $current_jobs = Job::where('status', 'pumped')
            ->where('truck_id', $truck->id)
            ->get();

        $truck_jobs = $this->getTruckJobs($truck);

        return view('work.work_admin', compact('jobs', 'truck', 'catchments', 'truck_jobs', 'current_jobs', 'user', 'drivers'));
    }

    // Metoda obsługująca pompowanie (wcześniej pump)
    public function processPump(Request $request)
    {
        // Walidacja danych
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'amount' => 'required|numeric|min:0.01',
            'user'   => 'sometimes|exists:users,id',
        ]);

        $this->preventDuplicateRequest($request, 'pump');

        DB::transaction(function () use ($request) {
            $user = Auth::user();
            if ($request->has('user')) {
                $user = User::findOrFail($request->input('user'));
            }
            $truck = $user->truck;
            $amount = $request->input('amount');
            $truck->amount += $amount;

            // Przypisanie job_id do pierwszego wolnego miejsca w ciężarówce
            foreach (range(1, 5) as $i) {
                $jobField = 'job_' . $i;
                if (is_null($truck->$jobField)) {
                    $truck->$jobField = $request->input('job_id');
                    break;
                }
            }
            $truck->save();

            $job = Job::findOrFail($request->input('job_id'));
            $job->pumped += $amount;
            $job->status = 'pumped';
            $job->truck_id = $truck->id;
            $job->cash = $request->has('cash');
            $job->price = $job->pumped * $job->address->zone->price;
            $job->save();

            $work = new Work;
            $work->job_id = $job->id;
            $work->amount = $amount;
            $work->user_id = $user->id;
            $work->truck_id = $truck->id;
            $work->save();

            if ($request->has('partial')) {
                $newJob = new Job;
                $newJob->address_id = $job->address_id;
                $newJob->status = 'Nowe';
                $newJob->schedule = $job->schedule;
                $newJob->driver_id = $job->driver_id;
                $newJob->comment = $job->comment . ' częściowo: ' . $job->pumped;
                $newJob->partial = $job->id;
                $newJob->save();
            }

            $client = $job->address->user;
            if (is_null($client->nip)) {
                if (!is_null($job->partial)) {
                    $originalJob = Job::findOrFail($job->partial);
                    $wz = Wz::findOrFail($originalJob->wz_id);
                    $wz->amount += $job->pumped;
                    $wz->price += $job->price;
                    $wz->save();

                    if ($request->has('cash')) {
                        $wz->paid = true;
                        $wz->cash = true;
                        $wz->save();
                    }
                } else {
                    $letter = $user->letter;
                    $month = Carbon::now()->format('m');
                    $year = Carbon::now()->format('Y');

                    $wzsCount = Wz::where('letter', $letter)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->count();
                    $number = $wzsCount + 1;

                    $address = $job->address;
                    $client = User::findOrFail($address->user_id);

                    $wz = new Wz;
                    $wz->number = $number;
                    $wz->month = $month;
                    $wz->year = $year;
                    $wz->letter = $letter;
                    $wz->job_id = $job->id;
                    $wz->client_name = $client->name;
                    $wz->userId = $client->id;
                    $wz->client_address = trim(($address->adres ?? '') . ' ' . ($address->numer ?? '') . ', ' . ($address->miasto ?? ''));
                    $wz->addressId = $address->id;
                    $wz->price = $job->price;
                    $wz->amount = $job->pumped;
                    $wz->sent = false;
                    $wz->paid = false;
                    $wz->save();

                    if ($request->has('cash')) {
                        $wz->paid = true;
                        $wz->cash = true;
                        $wz->save();
                    }

                    $job->wz_id = $wz->id;
                    $job->save();
                }
            }
        });

        return back();
    }

    // Metoda obsługująca wyrzucanie (wcześniej dump)
    public function processDump(Request $request)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'truck_id'     => 'required|exists:trucks,id',
            'catchment_id' => 'required|exists:catchments,id',
        ]);

        $this->preventDuplicateRequest($request, 'dump');

        DB::transaction(function () use ($request) {
            $user = Auth::user();
            if ($request->has('user')) {
                $user = User::findOrFail($request->input('user'));
            }

            $work = new Work;
            $work->amount = $request->input('amount');
            $work->truck_id = $request->input('truck_id');
            $work->user_id = $user->id;
            $work->type = 'dump';
            $work->save();

            $truck = $user->truck;
            $truck_jobs = $this->getTruckJobs($truck);

            $totalTruckAmount = $truck->amount;
            $dumpAmount = $request->input('amount');
            $difference = $totalTruckAmount - $dumpAmount;
            $differencePart = $totalTruckAmount > 0 ? $difference / $totalTruckAmount : 0;

            foreach ($truck_jobs as $truck_job) {
                $truck_job->corrected = $truck_job->pumped - ($truck_job->pumped * $differencePart);
                $truck_job->status = 'done';
                $truck_job->catchment_id = $request->input('catchment_id');
                $truck_job->save();
            }

            $truck->amount = 0;
            foreach (range(1, 5) as $i) {
                $truck->{'job_' . $i} = null;
            }
            $truck->save();
        });

        return back();
    }

    // Metoda aktualizująca status zlecenia (wcześniej status)
    public function updateJobStatus(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'status' => 'required|string',
        ]);

        $this->preventDuplicateRequest($request, 'status');

        DB::transaction(function () use ($request) {
            $job = Job::findOrFail($request->input('job_id'));
            $job->status = $request->input('status');
            $job->save();
        });

        return redirect('work');
    }

    // Metoda przypisująca kierowcę do zlecenia (wcześniej give)
    public function assignJobDriver(Request $request)
    {
        $request->validate([
            'job_id'    => 'required|exists:jobs,id',
            'driver_id' => 'required|exists:users,id',
        ]);

        $this->preventDuplicateRequest($request, 'give');

        DB::transaction(function () use ($request) {
            $job = Job::findOrFail($request->input('job_id'));
            $job->driver_id = $request->input('driver_id');
            $job->save();
        });

        return back();
    }

    // Metody zasobowe – placeholdery
    public function index() { }
    public function create() { }
    public function store(Request $request) { }
    public function show(Work $work) { }
    public function edit(Work $work) { }
    public function update(Request $request, Work $work) { }
    public function destroy(Work $work) { }
}
