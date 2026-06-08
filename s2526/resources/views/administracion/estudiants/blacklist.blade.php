@extends('administracion.layouts.dashboard.app')

@section('title')
    - Listado de Estudiantes
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    @include('administracion.estudiants.menus.crud')

                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    {{-- <u title="Listado especial con botones de acción">Listado</u> de Estudiantes formalmente inscritos --}}
                    <u title="Listado especial con botones de acción">Listado</u> de estudiantes con morosiadad y
                    restricciones administrativas.
                </h4>
            </div>

            <div class="card-body">

                {!! Form::open([
                    'route' => 'administracion.estudiants.blacklist',
                    'method' => 'GET',
                    'class' => '',
                    'role' => 'search',
                ]) !!}
                <div class="input-group">
                    {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Cédula']) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                        <button class="btn btn-dark my-2 my-sm-0" type="reset"><i class="fas fa-redo"
                                aria-hidden="true"></i></button>
                    </div>
                </div>
                {!! Form::close() !!}

                <hr>

                @include('administracion.estudiants.table.blacklist')

            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @parent
@endsection
