@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card mt-2">
            <div class="card-header alert-success">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">
                    @include('administracion.configuraciones.plan_beneficos.menus.index')
                </div>
                {{-- FIN Menu rapido --}}

                <h4>
                    <i class="{{ $icon_menus['estudiante'] }} fa-1x text-success"></i>
                    Asignar <span class=" font-weight-bolder">Plan Benéfico</span> a Estudiantes
                </h4>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'administracion.configuraciones.plan_beneficos.index',
                    'method' => 'GET',
                    'class' => '',
                    'id' => 'form-inscripcion-search',
                ]) !!}
                <div class="input-group">
                    {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Cédula']) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @isset($estudiants)
                    {{-- deck con el los usuarios encontrados --}}
                    @include('administracion.configuraciones.plan_beneficos.deck.estudiant')
                @endisset

            </div>

        </div>

    </main>
@endsection

@section('style')
    @parent
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        document.title = 'SAEFL - Plan Benéfico, Asignar';
    </script>
@endsection
