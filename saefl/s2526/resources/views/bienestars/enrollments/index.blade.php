@extends('bienestars.layouts.dashboard.app')

@section('title') Ficha del Estudiante - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    <livewire:bienestar.enrollment.index-component />
                </div>
            </div>
        </div>
    </main>
    
@endsection
