@extends('layout')

@section('title', 'Página no encontrada')

@section('content')
    <h1>Página no encontrada</h1>

    <a href="{{ route('users') }}">Regresar al inicio</a>
@endsection