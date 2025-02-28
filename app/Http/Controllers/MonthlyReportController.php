<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Address;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MonthlyReportController extends Controller
{
    public function filter()
    {
        $addresses = Address::all();
        return view('reports.filter', compact('addresses'));
    }

    public function index(Request $request)
    {
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $addressId    = $request->input('address_id');
        $showComments = $request->has('show_comments');
        $showPrice    = $request->has('show_price');

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

        if ($addressId) {
            // Gdy wybrano konkretny adres – grupowanie według partial
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
                    // Możemy też dodać liczbę zleceń w grupie
                    'count'    => $group->count(),
                ];
            })->values();
        } else {
            // Gdy nie wybrano adresu – grupowanie po adresie
            $aggregatedJobs = $jobs->groupBy(function($job) {
                return $job->address ? $job->address->id : 'brak';
            })->map(function($group) {
                $first = $group->first();
                return (object)[
                    'address' => $first->address,
                    'pumped'  => $group->sum('pumped'),
                    'price'   => $group->sum('price'),
                    'count'   => $group->count(), // liczba zleceń w danej grupie
                ];
            })->values();
        }

        // Możesz ewentualnie sortować $aggregatedJobs w zależności od potrzeb
        // np. po dacie schedule (tylko gdy $addressId != null)

        $totalJobs   = $aggregatedJobs->count();
        $totalAmount = $aggregatedJobs->sum('price');
        $totalPumped = $aggregatedJobs->sum('pumped');

        $addresses = Address::all();

        return view('reports.monthly_report', compact(
            'aggregatedJobs', 'dateFrom', 'dateTo', 'addressId',
            'totalJobs', 'totalAmount', 'totalPumped', 'addresses',
            'showComments', 'showPrice'
        ));
    }

    public function download(Request $request)
    {
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $addressId    = $request->input('address_id');
        $showComments = $request->has('show_comments');
        $showPrice    = $request->has('show_price');

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

        if ($addressId) {
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
                    'count'    => $group->count(),
                ];
            })->values();
        } else {
            $aggregatedJobs = $jobs->groupBy(function($job) {
                return $job->address ? $job->address->id : 'brak';
            })->map(function($group) {
                $first = $group->first();
                return (object)[
                    'address' => $first->address,
                    'pumped'  => $group->sum('pumped'),
                    'price'   => $group->sum('price'),
                    'count'   => $group->count(),
                ];
            })->values();
        }

        $totalJobs   = $aggregatedJobs->count();
        $totalAmount = $aggregatedJobs->sum('price');
        $totalPumped = $aggregatedJobs->sum('pumped');

        $pdf = Pdf::loadView('reports.monthly_report_pdf', compact(
            'aggregatedJobs', 'dateFrom', 'dateTo', 'addressId',
            'totalJobs', 'totalAmount', 'totalPumped', 'showComments', 'showPrice'
        ));

        $filename = "Raport_" . ($dateFrom ?? 'wszystkie') . "_do_" . ($dateTo ?? 'wszystkie') . "_adres_" . ($addressId ?? 'wszystkie') . ".pdf";
        return $pdf->download($filename);
    }
}
