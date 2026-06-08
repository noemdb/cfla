@extends('bienestars.layouts.dashboard.app')

@section('title') Historial del estudiante digitales, Sección Bienestar Estudiantil - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    <livewire:bienestar.estudiant.index-component />
                </div>
            </div>
        </div>
    </main>
    
@endsection
