{{-- @php $required_seccion = (!empty($required_seccion)) ? 'required':true ; @endphp
@php $required_lapso = (!empty($required_lapso)) ? 'required':true ; @endphp --}}

<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-6">Nombre/Identificador</div>
        <div class="col-4">Prosecución</div>
        {{-- <div class="col-2">Preinscripción</div> --}}
        {{-- <div class="col-2">Grado</div> --}}
        {{-- <div class="col-2">Sección</div> --}}
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-6">
            {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Identificador']) !!}
        </div>
        <div class="col-4">
            <div class="btn-group" role="group" aria-label="Button group">
                {!! Form::select('prosecucion_seccion_id', $list_prosecucion, $prosecucion_seccion_id, [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                ]) !!}

                {!! Form::select('status_prosecucion', ['SI' => 'SI', 'NO' => 'NO'], $status_prosecucion, [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>

        <div class="col-2">
            <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">Buscar</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
