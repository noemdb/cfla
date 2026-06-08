@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    @includeif('profesors.edescriptivas.menus.index')
                </div>
                <h3 class="pb-0 mb-0">
                    <i class="{{$icon_menus['edescriptivas'] ?? ''}} text-primary" aria-hidden="true"></i>
                    <u class="text-dark">Registro de </u> Evaluaciones Descriptivas
                </h3>
                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}} [{{ Auth::user()->profesor->id ?? '' }}]
                </span>
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('profesors.elements.forms.errors')
                @include('profesors.elements.messeges.oper_ok')

                @include('profesors.edescriptivas.form.search',[
                    'route'=>'profesors.edescriptivas.index',
                    'required_grado'=>true,
                    'required_seccion'=>true,
                    'btn_toprint_lote'=>true,
                    ])

                @include('profesors.edescriptivas.table.index')

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Profesor - Evaluación Descriptiva'; </script> @endsection



