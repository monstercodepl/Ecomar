<?php

namespace App\Http\Controllers;

use App\Models\PkDocument;
use App\Models\User;
use Illuminate\Http\Request;

class PkDocumentController extends Controller
{
    public function index(Request $request)
    {
        $pkDocuments = PkDocument::orderBy('created_at', 'desc')->get();
        $users = User::all();
        return view('pk_documents.index', compact('pkDocuments', 'users'));
    }
    
    public function create()
    {
        $users = User::all();
        return view('pk_documents.create', compact('users'));
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'letter'           => 'nullable|string|max:5',
            'month'            => 'required|string|max:2',
            'year'             => 'required|string|max:4',
            'user_id'          => 'required|exists:users,id',
            'adjustment_value' => 'required|numeric',
            'comment'          => 'nullable|string'
        ]);
        
        if (empty($data['letter'])) {
            $data['letter'] = 'P';
        }
        
        // Ustalanie automatycznego numeru dla danego zestawu (letter, month, year)
        $maxNumber = PkDocument::where('letter', $data['letter'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->max('number');
        $data['number'] = $maxNumber ? $maxNumber + 1 : 1;
        
        // Opcjonalnie, jeśli w bazie nadal istnieje kolumna "client_name",
        // możesz ustawić ją automatycznie na podstawie nazwy użytkownika:
        $user = User::find($data['user_id']);
        $data['client_name'] = $user ? $user->name : null;
        
        PkDocument::create($data);
        return redirect()->route('pk_documents.index')->with('success', 'Dokument PK został dodany.');
    }
}
