@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.profesors.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><u title="Listado especial con botones de acción">Listado</u> de <span
                        class="font-weight-bolder">Profesores</span> registrados</h4>

            </div>

            <div class="card-body">

                <div class="card-header p-1 m-1 alert-light">
                    {!! Form::open([
                        'route' => 'administracion.configuraciones.profesors.index',
                        'method' => 'GET',
                        'class' => 'p-1 m-1',
                        'role' => 'search',
                    ]) !!}
                    <div class="form-row font-weight-bold">
                        <div class="col-8">Nombre</div>
                        <div class="col-2">Grupo estable</div>
                        <div class="col-2">&nbsp;</div>
                    </div>
                    <div class="form-row">
                        <div class="col-8">
                            {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre']) !!}
                        </div>
                        <div class="col-2">
                            {!! Form::select('is_gestable', ['true' => 'SI', 'false' => 'NO'], $is_gestable, [
                                'class' => 'form-control',
                                'id' => 'is_gestable',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                Buscar
                            </button>
                        </div>

                    </div>
                    {!! Form::close() !!}
                </div>


                @include('administracion.configuraciones.profesors.table.index')

            </div>
        </div>
    </main>
@endsection

@section('title')
    Profesores, listado
@endsection
{{-- @section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Profesores, listado'; </script> @endsection --}}
