@extends('layout')

@section('title', 'Usuarios')

@section('content')

    <h1>{{ $title }}</h1>

    <ul>
        @forelse($users as $user)
            <li>{{ $user }}</li>
        @empty
            <li>No hay usuarios registrados.</li>
        @endforelse
    </ul>

@endsection

@section('sidebar')
    @parent

    <h1>Barra lateral personalizada</h1>
@endsection