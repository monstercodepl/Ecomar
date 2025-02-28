<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Address;
use App\Models\Wz; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class JobController extends Controller
{
    /**
     * Wyświetla stronę z tabelą DataTables
     */
    public function index()
    {
        return view('jobs.jobs');
    }

    /**
     * Pobiera dane do DataTables z obsługą filtrowania po dacie
     */
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

    /**
     * Generowanie raportu dziennego
     */
    public function daily()
    {
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs/daily', ['drivers' => $drivers]);
    }

    /**
     * Generowanie raportu ze zleceń
     */
    public function generate(Request $request)
    {
        $jobs = Job::whereRaw('date(schedule) =?', date($request->date))
            ->where('status', 'Nowe')
            ->when($request->driver_id, function ($query) use ($request) {
                return $query->where('driver_id', $request->driver_id);
            })
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('jobs/report', ['jobs' => $jobs, 'date' => $request->date]);
    }

    /**
     * Generowanie raportu z wykonanych zleceń
     */
    public function done_report()
    {
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs/done_report', ['drivers' => $drivers]);
    }

    public function generate_done_report(Request $request)
    {
        $jobs = Job::whereRaw('date(schedule) =?', date($request->date))
            ->when($request->driver_id, function ($query) use ($request) {
                return $query->where('driver_id', $request->driver_id);
            })
            ->get();

        return view('jobs/report_done', ['jobs' => $jobs, 'date' => $request->date]);
    }

    public function index_client()
    {
        $user = Auth::user();
        $jobs = Job::where('user_id', $user->id)->get();
        return view('jobs/my_jobs', ['jobs' => $jobs]);
    }

    public function create()
    {
        $addresses = Address::all();
        $users = User::all();
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs/new-job', ['addresses' => $addresses, 'users' => $users, 'drivers' => $drivers]);
    }

    public function store(Request $request)
    {
        Job::create([
            'address_id' => $request->address,
            'status' => 'Nowe',
            'schedule' => $request->date,
            'driver_id' => $request->driver,
            'comment' => $request->comment,
            'price' => $request->price,
        ]);

        return redirect()->route('jobs.index')->with('success', 'Zlecenie dodane.');
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);
        $drivers = User::whereNotNull('truck_id')->get();
        return view('jobs/job', ['job' => $job, 'drivers' => $drivers]);
    }

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
        $job->update($data);

        return redirect('jobs')->with('success', 'Zlecenie zostało zaktualizowane.');
    }

    public function destroy(Request $request)
    {
        Job::findOrFail($request->job_id)->delete();
        return redirect('jobs')->with('success', 'Zlecenie usunięte.');
    }
}
