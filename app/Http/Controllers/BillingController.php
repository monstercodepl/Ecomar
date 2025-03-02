<?php

namespace App\Http\Controllers;

use App\Models\Wz;
use App\Models\User;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
{
    // Pobieramy dokumenty WZ z przypisanymi płatnościami
    $wzQuery = \App\Models\Wz::with('payments');

    if ($request->filled('user_id')) {
        $wzQuery->where('userId', $request->input('user_id'));
    }
    if ($request->filled('date_from')) {
        $wzQuery->whereDate('created_at', '>=', $request->input('date_from'));
    }
    if ($request->filled('date_to')) {
        $wzQuery->whereDate('created_at', '<=', $request->input('date_to'));
    }
    $billings = $wzQuery->orderBy('created_at', 'desc')->get();
    
    // Pobieramy płatności niezwiązane z WZ z filtrem użytkownika
    $orphanQuery = \App\Models\Payment::whereNull('wz_id');

    if ($request->filled('user_id')) {
        $orphanQuery->where('user_id', $request->input('user_id'));
    }

    $orphanPayments = $orphanQuery->orderBy('payment_date', 'desc')->get();

    // Pobieramy dokumenty PK z filtrem użytkownika
    $pkQuery = \App\Models\PkDocument::orderBy('created_at', 'desc');
    
    if ($request->filled('user_id')) {
        $pkQuery->where('user_id', $request->input('user_id'));
    }

    $pkDocuments = $pkQuery->get();

    $users = \App\Models\User::all();

    return view('billings.index', compact('billings', 'users', 'orphanPayments', 'pkDocuments'));
}


    
}
