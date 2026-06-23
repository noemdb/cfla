@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-2">
                    @include('profesors.evaluacions.menus.crud')
                </div>
                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['crud'] ?? ''}} text-dark" aria-hidden="true"></i>
                    <u>Listado</u> de Evaluaciones registradas
                </h3>
                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}}
                </span>
            </div>

            <div class="card-body p-1 m-1">

                @include('profesors.elements.forms.errors')

                @include('profesors.elements.messeges.oper_ok')

                @include('profesors.evaluacions.partials.search',['route'=>'profesors.evaluacions.crud'])

                @include('profesors.evaluacions.table.crud')

            </div>
        </div>
    </main>

@endsection
