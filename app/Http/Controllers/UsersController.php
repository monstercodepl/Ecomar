<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\Truck;


class UsersController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);

        $addresses = Address::all();

        return view('users/user', ['user' => $user, 'addresses' => $addresses]);
    }

    public function read()
    {
        $users = User::all();
        return view('users/users', ['users' => $users]);
    }

    public function update(Request $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->secondary_email = $request->secondary_email;
        $user->default_email = $request->has('default_email');
        $user->phone = $request->phone;
        $user->nip = $request->nip;
        $user->save();

        $users = User::all();
        return redirect('user-management');;
    }

    public function create()
    {
        $addresses = Address::all();

        return view('users/new-user', ['addresses' => $addresses]);
    }

    public function save(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->secondary_email = $request->secondary_email;
        $user->default_email = $request->has('default_email');
        $user->phone = $request->phone;
        $user->password = bcrypt('1bnf7as9acsd6fgbvtea6');
        $user->nip = $request->nip;
        $user->save();
        
        $users = User::all();
        return redirect('user-management');
    }

    public function drivers()
    {
        $users = User::whereNotNull('truck_id')->get();

        return view('drivers/drivers', ['users' => $users]);
    }

    public function createDriver()
    {

        $users = User::where('truck_id', null)->get();
        $trucks = Truck::doesntHave('user')->get();

        return view('drivers/new-driver', ['users' => $users, 'trucks' => $trucks]);
    }

    public function saveDriver(Request $request)
    {
        $user = User::find($request->user);
        $user->truck_id = $request->truck;
        $user->save();

        $users = User::where('truck_id', true)->get();

        return view('drivers/drivers', ['users' => $users]);
    }
}