@extends('layout')

@section('title', "Editar usuario")

@section('content')
    @component('shared._card')
        @slot('header', 'Editar usuario')

        @include('shared._errors')

        <form method="POST" action="{{ url("usuarios/{$user->id}") }}">
            {{ method_field('PUT') }}

            @include('users._fields')

            <div class="form-group mt-4">
                <button class="btn btn-success" type="submit">Actualizar usuario</button>
                <a href="{{ route('users.index') }}" class="btn btn-info">Regresar al listado de usuarios</a>
            </div>
        </form>
    @endcomponent
@endsection