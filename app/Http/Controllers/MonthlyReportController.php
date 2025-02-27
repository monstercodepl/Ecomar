<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Address;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MonthlyReportController extends Controller
{
    /**
     * Wyświetla stronę filtra, gdzie można wybrać adres (opcjonalnie) oraz zakres dat,
     * a także opcję wyświetlania komentarzy.
     */
    public function filter()
    {
        $addresses = Address::all();
        return view('reports.filter', compact('addresses'));
    }

    /**
     * Generuje raport HTML.
     */
    public function index(Request $request)
    {
        $dateFrom    = $request->input('date_from');
        $dateTo      = $request->input('date_to');
        $addressId   = $request->input('address_id');
        $showComments = $request->has('show_comments');

        $query = Job::with('address');
        if ($dateFrom) {
            $query->whereDate('schedule', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('schedule', '<=', $dateTo);
        }
        if ($addressId) {
            $query->where('address_id', $addressId);
        }
        $jobs = $query->get();

        // Grupujemy zlecenia: jeśli pole partial jest ustawione, kluczem grupy jest wartość partial,
        // w przeciwnym razie grupujemy po własnym id.
        $groupedJobs = $jobs->groupBy(function($job) {
            return $job->partial ? $job->partial : $job->id;
        });

        // Dla każdej grupy tworzymy obiekt agregujący:
        $aggregatedJobs = $groupedJobs->map(function($group) {
            $first = $group->first();
            return (object)[
                'id'       => $first->id,
                'schedule' => $first->schedule,
                'address'  => $first->address, // załadowany przez eager loading
                'pumped'   => $group->sum('pumped'),
                'price'    => $group->sum('price'),
                'comment'  => $group->pluck('comment')->filter()->implode(' / '),
            ];
        })->values();

        $totalJobs   = $aggregatedJobs->count();
        $totalAmount = $aggregatedJobs->sum('price');
        $totalPumped = $aggregatedJobs->sum('pumped');

        $addresses = Address::all();

        return view('reports.monthly_report', compact(
            'aggregatedJobs', 'dateFrom', 'dateTo', 'addressId',
            'totalJobs', 'totalAmount', 'totalPumped', 'addresses', 'showComments'
        ));
    }

    /**
     * Generuje raport jako PDF.
     */
    public function download(Request $request)
    {
        $dateFrom    = $request->input('date_from');
        $dateTo      = $request->input('date_to');
        $addressId   = $request->input('address_id');
        $showComments = $request->has('show_comments');

        $query = Job::with('address');
        if ($dateFrom) {
            $query->whereDate('schedule', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('schedule', '<=', $dateTo);
        }
        if ($addressId) {
            $query->where('address_id', $addressId);
        }
        $jobs = $query->get();

        $groupedJobs = $jobs->groupBy(function($job) {
            return $job->partial ? $job->partial : $job->id;
        });

        $aggregatedJobs = $groupedJobs->map(function($group) {
            $first = $group->first();
            return (object)[
                'id'       => $first->id,
                'schedule' => $first->schedule,
                'address'  => $first->address,
                'pumped'   => $group->sum('pumped'),
                'price'    => $group->sum('price'),
                'comment'  => $group->pluck('comment')->filter()->implode(' / '),
            ];
        })->values();

        $totalJobs   = $aggregatedJobs->count();
        $totalAmount = $aggregatedJobs->sum('price');
        $totalPumped = $aggregatedJobs->sum('pumped');

        $pdf = Pdf::loadView('reports.monthly_report_pdf', compact(
            'aggregatedJobs', 'dateFrom', 'dateTo', 'addressId',
            'totalJobs', 'totalAmount', 'totalPumped', 'showComments'
        ));

        $filename = "Raport_" . ($dateFrom ?? 'wszystkie') . "_do_" . ($dateTo ?? 'wszystkie') . "_adres_" . ($addressId ?? 'wszystkie') . ".pdf";

        return $pdf->download($filename);
    }
}
