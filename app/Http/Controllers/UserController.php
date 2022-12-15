<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index()
    {
        $users = [
          'Joel',
          'Ellie',
          'Tess',
          'Tommy',
          'Bill',
        ];

        /*return view('users', [
            'users' => $users,
            'title' => 'Listado de Usuarios',
        ]);*/

        /*return view('users')
            ->with([
                'users' => $users,
                'title' => 'Listado de Usuarios',
            ]);*/

        /*return view('users')
            ->with('users', $users)
            ->with('title', 'Listado de Usuarios');*/

        $title = 'Listado de Usuarios';

        return view('users', compact('title', 'users'));
    }

    public function show($id)
    {
        return 'Mostrando detalles del usuario: ' . $id;
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
