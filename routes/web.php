<?php

Route::get('/', function () {
    return 'Home';
});

Route::get('/usuarios', 'UserController@index');

Route::get('/usuarios/nuevo', 'UserController@create');

Route::get('/usuarios/{id}', 'UserController@show');

Route::get('/usuarios/{id}/edit', function ($id) {
    return 'Editando usuario: ' . $id;
})->where('id', '[0-9]+');

Route::get('/saludo/{name}/{nickname?}', 'WelcomeUserController');