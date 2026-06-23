{{--

@extends('bienestars.layouts.dashboard.app')

@section('title')Proceso de Matriculación Escolar - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    <div class="card card-primary mt-2">
                        <div class="card-header pb-0 mb-0 alert-secondary">
                            <div class="btn-group float-right pt-0 pb-2">
                                @include('bienestars.matriculations.catchments.menus.index')
                            </div>
                            <h4><u title="Listado especial con botones de acción">Listado</u> de las <span class="font-weight-bolder">manifestaciones de interés</span> registradas</h4>
                        </div>

                        <div class="card-body">

                            <div class="py-2 my-2">


                                {!! Form::open(['route'=>'bienestars.matriculations.catchments.index','method'=>'GET','class'=>'', 'role'=>'search']) !!}
                                <div class="input-group">
                                    {!! Form::text('representant_ci', $representant_ci, ['class' => 'form-control','placeholder'=>'Buscar po Cédula']); !!}
                                    <div class="input-group-append" style="z-index: 0;">
                                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}

                            </div>

                            @include('bienestars.matriculations.catchments.table.index')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection
--}}

@extends('bienestars.layouts.dashboard.app')

@section('title')Proceso de Matriculación Escolar - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')
    @livewire('bienestar.matriculation.catchment-component')
@endsection



