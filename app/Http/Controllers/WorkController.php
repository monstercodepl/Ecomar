<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Job;
use App\Models\Truck;
use App\Models\Catchment;
use App\Models\Wz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkController extends Controller
{
    /**
     * Zapobiega powtórzeniu tego samego requestu (opcjonalnie).
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
     * Zwraca listę zleceń dla zalogowanego użytkownika (kierowcy) na dziś.
     */
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

        // Zlecenia, które są już "pumped" i przypisane do ciężarówki
        $current_jobs = Job::where('status', 'pumped')
            ->where('truck_id', $truck->id)
            ->get();

        // Przykładowe pobranie zleceń przypisanych do ciężarówki
        $truck_jobs = $this->getTruckJobs($truck);

        return view('work.work', compact('jobs', 'truck', 'catchments', 'truck_jobs', 'current_jobs', 'drivers'));
    }

    /**
     * Pomocnicza metoda pobierająca zlecenia przypisane do ciężarówki (job_1..job_5).
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

    /**
     * Metoda obsługująca wypompowanie (pump).
     * Tworzenie WZ dopiero przy drugiej części zlecenia.
     */
    public function pump(Request $request)
    {
        // (Opcjonalnie) Zabezpieczenie przed duplikacją:
        // $this->preventDuplicateRequest($request, 'pump');

        DB::transaction(function () use ($request) {
            $user = Auth::user();
            if ($request->has('user')) {
                $user = User::findOrFail($request->input('user'));
            }
            $truck = $user->truck;
            $amount = $request->input('amount');
            $truck->amount += $amount;

            // Przypisanie zlecenia do ciężarówki
            foreach (range(1, 5) as $i) {
                $jobField = 'job_' . $i;
                if (is_null($truck->$jobField)) {
                    $truck->$jobField = $request->input('job_id');
                    break;
                }
            }
            $truck->save();

            $job = Job::findOrFail($request->input('job_id'));
            // Aktualizacja wypompowanej ilości i ceny
            $job->pumped += $amount;
            $job->status = 'pumped';
            $job->truck_id = $truck->id;
            $job->cash = $request->has('cash');
            // Przykładowe obliczanie ceny na podstawie strefy
            $job->price = $job->pumped * $job->address->zone->price;
            $job->save();

            // Rejestracja pracy
            $work = new Work;
            $work->job_id = $job->id;
            $work->amount = $amount;
            $work->user_id = $user->id;
            $work->truck_id = $truck->id;
            $work->save();

            // Logika częściowa
            if ($request->has('partial') || !is_null($job->partial )) {
                // Sprawdzamy, czy zlecenie jest pierwszą czy drugą częścią
                if (is_null($job->partial)) {
                    // PIERWSZE pompowanie częściowe – tworzymy drugie zlecenie, NIE tworzymy WZ
                    $newJob = new Job;
                    $newJob->address_id = $job->address_id;
                    $newJob->status = 'Nowe';
                    $newJob->schedule = $job->schedule;
                    $newJob->driver_id = $job->driver_id;
                    $newJob->comment = $job->comment . ' (częściowo: ' . $job->pumped . 'm³)';
                    $newJob->partial = $job->id; // drugie zlecenie wskazuje na pierwsze
                    $newJob->save();
                } else {
                    // DRUGIE pompowanie częściowe – sumujemy wartości i tworzymy nową WZ
                    $mainJob = Job::findOrFail($job->partial);
                    // Odświeżamy rekordy w bazie, aby mieć najnowsze dane
                    $mainJob->refresh();
                    $job->refresh();

                    $aggregatedPumped = $mainJob->pumped + $job->pumped;
                    $aggregatedPrice = $mainJob->price + $job->price;

                    // Tworzymy nową WZ przypisaną do drugiej części
                    $letter = $user->letter ?? 'A';
                    $month = date('m');
                    $year = date('Y');
                    $wzsCount = Wz::where('letter', $letter)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->count();
                    $number = $wzsCount + 1;

                    $address = $job->address;
                    $client = $address->user;

                    $newWz = Wz::create([
                        'number'         => $number,
                        'month'          => $month,
                        'year'           => $year,
                        'letter'         => $letter,
                        'job_id'         => $job->id, // przypisujemy do drugiej części
                        'client_name'    => $client->name,
                        'userId'         => $client->id,
                        'client_address' => trim(($address->adres ?? '') . ' ' . ($address->numer ?? '') . ', ' . ($address->miasto ?? '')),
                        'addressId'      => $address->id,
                        'price'          => $aggregatedPrice,
                        'amount'         => $aggregatedPumped,
                        'sent'           => false,
                        'paid'           => false,
                    ]);
                    if ($request->has('cash')) {
                        $newWz->paid = true;
                        $newWz->cash = true;
                        $newWz->save();
                    }
                    // Przypisujemy utworzoną WZ do drugiego zlecenia
                    $job->update(['wz_id' => $newWz->id]);
                }
            } else {
                // Zlecenie pojedyncze – tworzymy WZ tylko jeśli jeszcze nie istnieje
                if (!$job->wz_id) {
                    $letter = $user->letter ?? 'A';
                    $month = date('m');
                    $year = date('Y');
                    $wzsCount = Wz::where('letter', $letter)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->count();
                    $number = $wzsCount + 1;

                    $address = $job->address;
                    $client = $address->user;

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
                    // Przypisujemy utworzoną WZ do tego zlecenia
                    $job->update(['wz_id' => $wz->id]);
                }
            }
        });

        return back();
    }

    /**
     * Metoda obsługująca zlewanie (dump).
     */
    public function dump(Request $request)
    {
        // Przykładowa logika
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

    /**
     * Aktualizacja statusu zlecenia.
     */
    public function status(Request $request)
    {
        // $this->preventDuplicateRequest($request, 'status');
        DB::transaction(function () use ($request) {
            $job = Job::findOrFail($request->input('job_id'));
            $job->status = $request->input('status');
            $job->save();
        });

        return redirect('work');
    }

    /**
     * Wyświetla widok do wyboru kierowcy.
     */
    public function select()
    {
        $drivers = User::whereNotNull('truck_id')->get();
        return view('work.select', compact('drivers'));
    }

    /**
     * Wyświetla zlecenia kierowcy w trybie admin.
     */
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

    /**
     * Przypisywanie zlecenia do innego kierowcy (admin).
     */
    public function give(Request $request)
    {
        // $this->preventDuplicateRequest($request, 'give');
        DB::transaction(function () use ($request) {
            $job = Job::findOrFail($request->input('job_id'));
            $job->driver_id = $request->input('driver_id');
            $job->save();
        });

        return back();
    }
}
