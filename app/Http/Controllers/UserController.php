<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index()
    {
        if (request()->has('empty')) {
            $users = [];
        } else {
            $users = [
                'Joel', 'Ellie', 'Tess', 'Tommy', 'Bill',
            ];
        }

        $title = 'Listado de Usuarios';

        return view('users', compact('title', 'users'));
    }

    public function show($id)
    {
        $title = 'Detalles de usuarios';

        return view('show', compact('title', 'id'));
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