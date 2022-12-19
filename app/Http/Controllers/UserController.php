<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        /*if (request()->has('empty')) {
            $users = [];
        } else {
            $users = [
                'Joel', 'Ellie', 'Tess', 'Tommy', 'Bill',
            ];
        }*/

        //$users = DB::table('users')->get();

        $users = User::all();

        $title = 'Listado de Usuarios';

        return view('users.index', compact('title', 'users'));
    }

    public function show($id)
    {
        return view('users.show', compact('id'));
    }

    public function create()
    {
        return 'Creando nuevo usuario';
    }

    public function edit($id)
    {
        return 'Editando usuario: ' . $id;
    }
}