<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeUserController extends Controller
{
    public function index($name, $nickname)
    {
        $name = ucfirst($name);

        return 'Bienvenido ' . $name . ' tu apodo es ' . $nickname . '.';
    }

    public function welcomeWithoutNick($name)
    {
        $name = ucfirst($name);

        return 'Bienvenido ' . $name;
    }
}
