@extends('bienestars.layouts.dashboard.app')

@section('title')Proceso de Matriculación Escolar - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="container-fluid">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('bienestars.matriculations.interviews.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><u title="Listado especial con botones de acción">Gestión</u> de la <span class="font-weight-bolder">Entrevista</span> registrada</h4>
            </div>

            <div class="card-body">

            @include('bienestars.elements.forms.errors')

            @include('bienestars.elements.messeges.oper_ok')

            {!! Form::model($catchment_interview,['route' => ['bienestars.matriculations.interviews.update',$catchment_interview->id], 'method' => 'PUT', 'id'=>'form-update-catchment_interview'.$catchment_interview->id,'role'=>'form']) !!}

            <div class="card bd-callout bd-callout-primary">
                <h4 class="card-header">
                    <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                    Actualizar Entrevista
                </h4>

                <div class="card-body">

                    @include('bienestars.matriculations.interviews.form.fields')

                    {{-- @include('bienestars.matriculations.interviews.partials.info') --}}

                </div>

                <button type="submit" class="btn-estudiant-create btn btn-primary btn-block" value="Actualizar"
                    data-id="create" id="btn-create-estudiant-{{$estudiant->id ?? ''}}">
                    <i class="far fa-save"></i>
                    Actualizar
                </button>

            </div>

            {!! Form::close() !!}

            </div>
        </div>
    </main>


@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection
