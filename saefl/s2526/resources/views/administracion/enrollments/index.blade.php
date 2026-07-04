@extends('administracion.layouts.dashboard.app')

@section('title') Formatos de Renovación de Matrícula - Imprimir @endsection

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    <i class="{{ $icon_menus['enrollments'] }} fa-1x text-success"></i>
                    Imprimir formatos de Renovación de Matrícula
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                        @include('administracion.enrollments.menus.index')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.enrollments.form.search.index',['route'=>'administracion.enrollments.index','btn_toprint'=>'true'])

                @if (empty($seccion))
                    <h6 class=" font-weight-bold text-muted">Listado de estudiantes inscritos formalmente. <span class="small">Total: {{$estudiants->count()}}</span></h6>
                @endif
                @include('administracion.enrollments.table.index')

            </div>

        </div>

    </main>

@endsection

@section('scripts')
    @parent
@endsection
