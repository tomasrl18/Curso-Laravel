<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = 'Listado de Usuarios';

        return view('users.index', compact('title', 'users'));
    }

    public function show(User $user)
    {
        //$user = User::findOrFail($id);

        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store()
    {
        //$data = request()->all();

        /*if(empty($data['name'])) {
            return redirect('usuarios/nuevo')->withErrors([
                'name' => 'El campo nombre es obligatorio'
            ]);
        }*/

        $data = request()->validate([
            'name' => 'required',
            'email' => '',
            'password' => '',
        ], [
            'name.required' => 'El campo nombre es obligatorio',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return redirect()->route('users.index');
    }
}