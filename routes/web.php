<?php

Route::get('/', function () {
    return 'Home';
});

Route::get('/usuarios', 'UserController@index')
    ->name('users');

Route::get('/usuarios/nuevo', 'UserController@create')
    ->name('users.create');

Route::get('/usuarios/{id}', 'UserController@show')
    ->name('users.show');

Route::get('/usuarios/{id}/edit', 'UserController@edit');

Route::get('/saludo/{name}/{nickname}', 'WelcomeUserController@index');

Route::get('/saludo/{name}/', 'WelcomeUserController@welcomeWithoutNick');