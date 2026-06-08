@extends('bienestars.layouts.dashboard.app')

@section('title') Incidencias del Estudiante - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main">
        {{-- <h3>Registro de incidentes estudiantiles.</h3> --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    <livewire:bienestar.incident.index-component />
                </div>
            </div>
        </div>
    </main>
    
@endsection
