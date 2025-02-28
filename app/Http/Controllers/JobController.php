<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Address;
use App\Models\Wz; // Upewnij się, że używasz właściwego modelu Wz
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('jobs.jobs');
    }

    public function getJobs(Request $request)
    {
        $query = Job::with(['address', 'address.user', 'driver', 'wz'])->select('jobs.*');

        // Filtracja po dacie (jeśli ustawiona)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('schedule', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('schedule', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('schedule', '<=', $request->end_date);
        }

        return DataTables::eloquent($query)
            ->addColumn('actions', function ($job) {
                return '
                    <button type="button" class="btn bg-danger text-white btn-md" data-bs-toggle="modal" data-bs-target="#deleteModal'.$job->id.'">
                         Usuń
                    </button>
                    <div class="modal fade" id="deleteModal'.$job->id.'" tabindex="-1" aria-labelledby="deleteModalLabel'.$job->id.'" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel'.$job->id.'">Potwierdzenie usunięcia</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
                                </div>
                                <div class="modal-body">
                                    Czy na pewno chcesz usunąć to zlecenie?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                                    <form method="POST" action="job/delete">
                                        '.csrf_field().'
                                        <input type="hidden" name="job_id" value="'.$job->id.'">
                                        <button type="submit" class="btn btn-danger">Usuń</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="'.route('job', ['id' => $job->id]).'"><button class="btn bg-warning btn-md">Edytuj</button></a>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function daily()
    {
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs/daily', ['drivers' => $drivers]);
    }

    public function generate(Request $request)
    {
        if($request->driver_id == ''){
            $jobs = Job::whereRaw('date(schedule) =?', date($request->date))
                ->where('status', 'Nowe')
                ->orderBy('updated_at', 'asc')
                ->get();
        } else {
            $jobs = Job::where('driver_id', $request->driver_id)
                ->whereRaw('date(schedule) =?', date($request->date))
                ->where('status', 'Nowe')
                ->orderBy('updated_at', 'asc')
                ->get();
        }
        return view('jobs/report', ['jobs' => $jobs, 'date' => $request->date]);
    }

    public function done_report()
    {
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs/done_report', ['drivers' => $drivers]);
    }

    public function generate_done_report(Request $request)
    {
        if($request->driver_id == ''){
            $jobs = Job::whereRaw('date(schedule) =?', date($request->date))->get();
        } else {
            $jobs = Job::where('driver_id', $request->driver_id)
                ->whereRaw('date(schedule) =?', date($request->date))
                ->get();
        }
        return view('jobs/report_done', ['jobs' => $jobs, 'date' => $request->date]);
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
        $job->comment = $request->comment;
        $job->price = $request->price;
        $job->save();

        $jobs = Job::all();
        return view('jobs/jobs', ['jobs' => $jobs]);
    }

    /**
     * Display the specified resource.
     * Używamy metody show do wyświetlania formularza edycji.
     * Jeśli zlecenie jest częścią częściowego, przekierowujemy do widoku edycji obu części.
     */
    public function show($id)
    {
        $job = Job::findOrFail($id);

        // Jeśli to główne zlecenie ma drugą część, przekierowujemy do edycji częściowego
        if (empty($job->partial)) {
            $secondJob = Job::where('partial', $job->id)->first();
            if ($secondJob) {
                return redirect()->route('jobs.editPartial', ['id' => $job->id]);
            }
        } else {
            // Jeśli to jest druga część, przekierowujemy do edycji głównego zlecenia
            return redirect()->route('jobs.editPartial', ['id' => $job->partial]);
        }

        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs/job', ['job' => $job, 'drivers' => $drivers]);
    }

    /**
     * Standardowa metoda edycji (nieużywana, bo korzystamy z show).
     */
    public function edit(Job $job)
    {
        //
    }

    /**
     * Aktualizuje pojedyncze zlecenie (tryb standardowy).
     * Tylko price i pumped zapisujemy do WZ w polach price i amount, pomijając komentarz.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'id'      => 'required|exists:jobs,id',
            'date'    => 'required|date',
            'driver'  => 'nullable|exists:users,id',
            'comment' => 'nullable|string',
            'pumped'  => 'required|numeric',
            'price'   => 'required|numeric',
        ]);

        $job = Job::findOrFail($data['id']);
        $job->update([
            'schedule'  => $data['date'],
            'driver_id' => $data['driver'],
            'comment'   => $data['comment'],
            'pumped'    => $data['pumped'],
            'price'     => $data['price'],
        ]);

        // Aktualizacja WZ, jeśli checkbox update_wz jest zaznaczony
        if ($request->has('update_wz') && $job->wz_id) {
            $wz = Wz::find($job->wz_id);
            if ($wz) {
                $wz->update([
                    // price → job->price
                    'price'  => $job->price,
                    // amount → job->pumped
                    'amount' => $job->pumped,
                    // Komentarz pomijamy, bo tak sobie zażyczyłeś
                ]);
            }
        }

        return redirect('jobs')->with('success', 'Zlecenie zostało zaktualizowane.' .
            ($request->has('update_wz') ? ' Przypisana WZ została również zaktualizowana.' : ''));
    }

    /**
     * Aktualizuje oba rekordy zlecenia częściowego jednocześnie.
     * Sumujemy price i pumped z obu części i zapisujemy do WZ w polach price i amount.
     * Komentarz nie jest zapisywany w WZ.
     */
    public function updatePartial(Request $request)
    {
        $data = $request->validate([
            'main_id'        => 'required|exists:jobs,id',
            'main_date'      => 'required|date',
            'main_driver'    => 'nullable|exists:users,id',
            'main_comment'   => 'nullable|string',
            'main_pumped'    => 'required|numeric',
            'main_price'     => 'required|numeric',
            'second_id'      => 'nullable|exists:jobs,id',
            'second_date'    => 'nullable|date',
            'second_driver'  => 'nullable|exists:users,id',
            'second_comment' => 'nullable|string',
            'second_pumped'  => 'nullable|numeric',
            'second_price'   => 'nullable|numeric',
        ]);

        // Główne zlecenie
        $mainJob = Job::findOrFail($data['main_id']);
        $mainJob->update([
            'schedule'  => $data['main_date'],
            'driver_id' => $data['main_driver'],
            'comment'   => $data['main_comment'],
            'pumped'    => $data['main_pumped'],
            'price'     => $data['main_price'],
        ]);

        // Druga część, jeśli istnieje
        $secondJob = null;
        if (!empty($data['second_id'])) {
            $secondJob = Job::findOrFail($data['second_id']);
            $secondJob->update([
                'schedule'  => $data['second_date'] ?? $data['main_date'],
                'driver_id' => $data['second_driver'] ?? $data['main_driver'],
                'comment'   => $data['second_comment'] ?? $data['main_comment'],
                'pumped'    => $data['second_pumped'] ?? $data['main_pumped'],
                'price'     => $data['second_price'] ?? $data['main_price'],
            ]);
        }

        // Checkbox "update_wz"
        if ($request->has('update_wz')) {
            // Czy główne lub drugie ma wz_id
            $wzId = $mainJob->wz_id;
            if (!$wzId && $secondJob) {
                $wzId = $secondJob->wz_id;
            }
            if ($wzId) {
                $wz = Wz::find($wzId);
                if ($wz) {
                    // Suma price i pumped z obu części
                    $aggregatedPrice = $mainJob->price;
                    $aggregatedPumped = $mainJob->pumped;
                    // Komentarz w WZ pomijamy
                    if ($secondJob) {
                        $aggregatedPrice += $secondJob->price;
                        $aggregatedPumped += $secondJob->pumped;
                    }
                    $wz->update([
                        'price'  => $aggregatedPrice,
                        'amount' => $aggregatedPumped,
                        // Komentarz w WZ pomijamy
                    ]);
                }
            }
        }

        return redirect()->route('jobs')
            ->with('success', 'Zlecenie częściowe zostało zaktualizowane.' . 
                ($request->has('update_wz') ? ' WZ również zaktualizowana (bez komentarza).' : ''));
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

    /**
     * Wyświetla formularz do jednoczesnej edycji obu części zlecenia.
     */
    public function editPartial($id)
    {
        $mainJob = Job::findOrFail($id);
        $secondJob = Job::where('partial', $id)->first();
        return view('jobs.edit_partial', compact('mainJob', 'secondJob'));
    }
}
