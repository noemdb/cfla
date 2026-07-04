@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.configuraciones.asignaturas.menus.create')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Registrar una nueva <span class="font-weight-bolder">Asignatura</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">
                    <h5 class="card-header">
                        <i class="{{ $icon_menus['nuevo'] }} fa-1x"></i>
                        Datos
                    </h5>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-8">
                                {!! Form::open([
                                    'route' => 'administracion.configuraciones.asignaturas.store',
                                    'method' => 'POST',
                                    'id' => 'form-inscripcion-create',
                                    'class' => 'form-signin',
                                ]) !!}
                                @include('administracion.configuraciones.asignaturas.form.fields')
                                {!! Form::submit('Registrar', ['class' => 'btn-grupo_estable-create btn btn-primary btn-block']) !!}
                                {!! Form::close() !!}
                            </div>
                            <div class="col-4">
                                {{-- @include('administracion.configuraciones.plan_beneficos.deck.card.benefico') --}}
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        document.title = 'SAEFL - Asignatura, registrar';
    </script>
@endsection
