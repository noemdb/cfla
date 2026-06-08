@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Lista de pases</h1>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Tipo</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Duración</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($pases as $pase)
                <tr>
                    <td>{{ $pase->id }}</td>
                    <td>{{ $pase->estudiante->name }}</td>
                    <td>{{ $pase->type }}</td>
                    <td>{{ $pase->motive }}</td>
                    <td>{{ $pase->date }}</td>
                    <td>{{ $pase->time }}</td>
                    <td>{{ $pase->duration }}</td>
                    <td>{{ $pase->status }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
