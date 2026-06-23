@extends('layouts.errors')

@section('content')
<div class="container mt-5">
    <div class="alert alert-danger text-center">
        <h4 class="alert-heading">Estudiante no encontrado</h4>
        <p>No se encontró ningún estudiante con el token proporcionado.</p>
        <hr>
        <p class="mb-0">Verifica el enlace o comunícate con la institución educativa.</p>
    </div>
</div>
@endsection
