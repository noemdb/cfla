@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h4>Listado de Estudiantes, Servicios Ejecutados Acción Comunitaria</h4>
            </div>

            {!! Form::open([
                'route' => 'administracion.social_actions.listado',
                'method' => 'GET',
                'class' => 'p-2',
                'role' => 'search',
            ]) !!}
            <div class="form-row font-weight-bold">
                <div class="col-8">Grado/Sección</div>
                <div class="col-4">&nbsp;</div>
            </div>
            <div class="form-row">

                <div class="col-8">
                    <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                        {!! Form::select('grado_id', $list_grado, $grado_id, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'grado_id',
                            'placeholder' => 'Seleccione',
                            'required',
                        ]) !!}
                    </div>
                </div>

                <div class="col-4">
                    <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                        <button class="btn btn-primary btn-block" type="submit">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

            <div class="card-body">

                @include('administracion.social_actions.table.listado')

                @include('administracion.datatables.particulars.representans.exportBootstrap')

            </div>
        </div>
    </main>
@endsection
