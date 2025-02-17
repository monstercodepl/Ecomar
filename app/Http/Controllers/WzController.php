<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Wz;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WzController extends Controller
{
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
