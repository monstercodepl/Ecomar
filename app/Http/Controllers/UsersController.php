<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;


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
        $user->phone = $request->phone;
        $user->address_id = $request->address;
        $user->save();

        $users = User::all();
        return view('users/users', ['users' => $users]);
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
        $user->phone = $request->phone;
        $user->password = bcrypt('1bnf7as9acsd6fgbvtea6');
        $user->address_id = $request->address;
        $user->save();
        
        $users = User::all();
        return view('users/users', ['users' => $users]);
    }
}