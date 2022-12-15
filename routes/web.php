<?php

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', function () {
    return 'Home';
});

Route::get('/usuarios', function () {
    return 'Usuarios';
});

Route::get('/usuarios/nuevo', function () {
    return 'Creando nuevo usuario';
});

Route::get('/usuarios/{id}', function ($id) {
    return 'Mostrando detalles del usuario: ' . $id;
});

Route::get('/saludo/{name}/{nickname?}', function ($name, $nickname = null) {
    $name = ucfirst($name);

    if ($nickname) {
        return 'Bienvenido ' . $name . ' tu apodo es ' . $nickname . '.';
    } else {
        return 'Bienvenido ' . $name . '.';
    }
});