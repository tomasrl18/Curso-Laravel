@extends('layout')

@section('title', "Crear usuario")

@section('content')
    <div class="card">
        <h4 class="card-header">
            Crear usuario
        </h4>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6>Por favor corrige los errores debajo:</h6>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('usuarios') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input class="form-control" type="text" name="name" id="name" placeholder="Pedro Perez" value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input class="form-control" type="email" name="email" id="email" placeholder="pedro@example.com" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input class="form-control" type="password" name="password" id="password" placeholder="Mayor a 6 carácteres">
                </div>

                <div class="form-group">
                    <label for="bio">Biografía:</label>
                    <textarea class="form-control" name="bio" id="bio" placeholder="Soy desarrollador web..." {{ old('bio') }}></textarea>
                </div>

                <div class="form-group">
                    <label for="profession_id">Profesión</label>
                    <select name="profession_id" id="profession_id" class="form-control">
                        <option value="">Seleccione una opción</option>
                        @foreach($professions as $profession)
                            <option value="{{ $profession->id }}"{{ old('profession_id') == $profession->id ? ' selected' : '' }}>
                                {{ $profession->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="twitter">Twitter:</label>
                    <input type="text" class="form-control" name="twitter" id="twitter" placeholder="https://twitter.com/tomasrl" {{ old('twitter') }} />
                </div>

                <h5>Habilidades</h5>

                @foreach($skills as $skill)
                    <div class="form-check form-check-inline">
                        <input name="skills[{{ $skill->id }}]"
                               class="form-check-input"
                               type="checkbox"
                               id="skill_{{ $skill->id }}"
                               value="{{ $skill->id }}"
                               {{ old("skills.{$skill->id}") ? 'checked' : '' }}>
                        <label class="form-check-label" for="skill_{{ $skill->id }}">{{ $skill->name }}</label>
                    </div>
                @endforeach

                <h5 class="mt-3">Rol</h5>

                @foreach($roles as $role => $name)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                               type="radio"
                               name="role"
                               id="role_{{ $role }}"
                               value="{{ $role }}"
                               {{ old('role') == $role ? 'checked' : '' }}>
                        <label class="form-check-label" for="role_{{ $role }}">{{ $name }}</label>
                    </div>
                @endforeach

                <div class="form-group mt-4">
                    <button class="btn btn-success" type="submit">Crear usuario</button>
                    <a href="{{ route('users.index') }}" class="btn btn-info">Regresar al listado de usuarios</a>
                </div>

            </form>
        </div>
    </div>

@endsection