@extends('layout')

@section('title', "Editar usuario")

@section('content')
    <div class="card">
        <h4 class="card-header">Editar usuario</h4>

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <h5>Por favor, corrige los errores:</h5>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url("usuarios/{$user->id}") }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input class="form-control" type="text" name="name" id="name" placeholder="Pedro Perez" value="{{ old('name', $user->name) }}">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input class="form-control" type="email" name="email" id="email" placeholder="pedro@example.com" value="{{ old('email', $user->email) }}">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input class="form-control" type="password" name="password" id="password" placeholder="Mayor a 6 carácteres">
                </div>

                <button class="btn btn-success" type="submit">Actualizar usuario</button>

                <a class="btn btn-info" href="{{ route('users.index') }}">Regresar al listado de usuarios</a>
            </form>

        </div>
    </div>
@endsection