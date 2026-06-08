@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.cuentaxpagars.menus.create')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Datos del <span class=" font-weight-bolder">Concepto de Cobro</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">

                    <h5 class="card-header">Datos</h5>

                    <div class="card-body">
                        {!! Form::open(['route' => 'administracion.configuraciones.cuentaxpagars.store', 'method' => 'POST', 'id'=>'form-cuentaxpagars-create', 'class'=>'form-signin']) !!}

                        @if (isset($estudiant_id))
                            {{ Form::hidden('estudiant_id', $estudiant->id) }}
                        @endif

                        @include('administracion.configuraciones.cuentaxpagars.form.field')

                        <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
                            <i class="far fa-save"></i>
                            Registrar
                        </button>

                        {!! Form::close() !!}

                    </div>
                </div>

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Conceptos de Cobro, crear'; </script> @endsection
