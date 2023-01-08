@extends('layout')

@section('title', "Crear usuario")

@section('content')
    @component('shared._card')
        @slot('header', 'Crear usuario')

        @include('shared._errors')

        <form method="POST" action="{{ url('usuarios') }}">
            @render('UserFields', ['user' => $user])

            <div class="form-group mt-4">
                <button class="btn btn-success" type="submit">Crear usuario</button>
                <a href="{{ route('users.index') }}" class="btn btn-info">Regresar al listado de usuarios</a>
            </div>
        </form>
    @endcomponent
@endsection