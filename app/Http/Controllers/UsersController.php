<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\Truck;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Zapobiega wielokrotnemu wysyłaniu tego samego żądania.
     *
     * @param Request $request
     * @param string $actionKey
     * @param int $seconds Czas blokady w sekundach
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

    public function show($id)
    {
        $user = User::findOrFail($id);
        $addresses = Address::all();
        return view('users.user', compact('user', 'addresses'));
    }

    public function read()
    {
        $users = User::all();
        return view('users.users', compact('users'));
    }

    public function update(Request $request)
    {
        // Zabezpieczenie przed duplikacją requestu
        $this->preventDuplicateRequest($request, 'updateUser');

        $request->validate([
            'id'              => 'required|exists:users,id',
            'name'            => 'required|string|max:255',
            'email'           => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->input('id'))
            ],
            'secondary_email' => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'nip'             => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::findOrFail($request->input('id'));
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->secondary_email = $request->input('secondary_email');
            $user->default_email = $request->has('default_email');
            $user->phone = $request->input('phone');
            $user->nip = $request->input('nip');
            $user->save();
        });

        return redirect()->route('user-management');
    }

    public function create()
    {
        $addresses = Address::all();
        return view('users.new-user', compact('addresses'));
    }

    public function save(Request $request)
    {
        // Zabezpieczenie przed duplikacją requestu
        $this->preventDuplicateRequest($request, 'saveUser');

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'secondary_email' => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'nip'             => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request) {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->secondary_email = $request->input('secondary_email');
            $user->default_email = $request->has('default_email');
            $user->phone = $request->input('phone');
            // Hasło domyślne – rozważ bardziej bezpieczne rozwiązanie
            $user->password = bcrypt('1bnf7as9acsd6fgbvtea6');
            $user->nip = $request->input('nip');
            $user->save();
        });
        
        return redirect()->route('user-management');
    }

    public function drivers()
    {
        $users = User::whereNotNull('truck_id')->get();
        return view('drivers.drivers', compact('users'));
    }

    public function createDriver()
    {
        $users = User::whereNull('truck_id')->get();
        $trucks = Truck::doesntHave('user')->get();
        return view('drivers.new-driver', compact('users', 'trucks'));
    }

    public function saveDriver(Request $request)
    {
        // Zabezpieczenie przed duplikacją requestu
        $this->preventDuplicateRequest($request, 'saveDriver');

        $request->validate([
            'user'  => 'required|exists:users,id',
            'truck' => 'required|exists:trucks,id',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::findOrFail($request->input('user'));
            $user->truck_id = $request->input('truck');
            $user->save();
        });

        // Po zapisaniu przekieruj zamiast renderować widok
        return redirect()->route('drivers');
    }
}
