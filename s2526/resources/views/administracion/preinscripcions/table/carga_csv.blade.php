@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_fecha="d-none d-md-table-cell";
    $class_estudiant="d-none d-md-table-cell";
    $class_grado="d-none d-lg-table-cell text-nowrap";
    $class_action="nosort text-center";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"> {{-- table-hover  --}}

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_N }} text-nowrap">Fila</th>
            <th class="{{ $class_fecha }} text-nowrap">Fecha</th>
            <th class="{{ $class_representant }} text-wrap">Representante</th>
            <th class="{{ $class_estudiant }} text-nowrap">Estudiante</th>
            <th class="{{ $class_grado }} text-nowrap">Grado</th>
            <th class="{{ $class_grado }}">Telefono</th>
            <th class="{{ $class_grado }}">Comentario</th>
            <th class="{{ $class_grado }}">Incidencias</th>
            <th class="{{ $class_grado }}">Registrar</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @php $count_error = 0; @endphp

        @foreach($preinscripcionsCSV as $preinscripcionCSV)

            @php
            $class_error = null;
            $data = collect();
            $errors = $preinscripcionCSV['errors'];
            $datas = $preinscripcionCSV['datas'];
            $class_error = (count($errors)) ? $errors[0]['class']:null;
            $class_error = ($errors->search('PINS') || $errors->search('INSC')) ? 'secondary':$class_error;
            @endphp

            {{-- @include('administracion.preinscripcions.table.carga_csv.errors') --}}

            <tr data-id="{{ $datas['representant_id'] ?? '' }}" class="table-{{ $class_error ?? ''}}">

                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                    {{-- {{ $datas }} --}}
                </td>
                
                <td class=" font-weight-bold text-wrap {{ $class_N  ?? ''}}">
                    {{ ($datas['index'] + 2) }}
                </td>

                <td class=" font-weight-bold text-wrap {{ $class_N  ?? ''}}">
                    {{ $datas['fecha'] ?? '' }}
                </td>

                <td class=" font-weight-bold text-wrap {{ $class_representant  ?? ''}}">
                    {{ $datas['representant_name'] ?? '' }} <br>
                    <span class="small font-weight-bold text-muted">
                        {{ $datas['representant_ci'] ?? '' }}
                    </span>
                </td>

                <td class=" font-weight-bold text-wrap {{ $class_estudiant  ?? ''}}">
                    {{ $datas['estudiant_name'] ?? '' }} <br>
                    <span class="small font-weight-bold text-muted">
                        {{ $datas['ci_estudiant'] ?? '' }}
                    </span>
                </td>

                <td class="{{ $class_grado }}">
                    {{$datas['data_grado_name'] ?? ''}} <br>
                    <span class="small font-weight-bold text-muted">
                        {{ $datas['grupo_estable_name'] ?? '' }}
                    </span>
                </td>
                <td class="{{ $class_grado }}" title="{{$datas['representant_phones'] ?? ''}}">
                    {{ Str::limit($datas['representant_phones'],12,'...') }}
                </td>

                <td class="{{ $class_grado }}" title="{{$datas['comments'] ?? ''}}">
                    {{ Str::limit($datas['comments'],40,'...') }}
                </td>

                <td class=" text-wrap {{ $class_representant ?? '' }}">

                    @if (count($errors) > 0)
                        @include('administracion.preinscripcions.table.carga_csv.errors.error_f2')                        
                    @endif
                    
                </td>
                <td class="text-center {{ $class_representant ?? '' }} ">
                    @if (count($errors) <= 0)
                        @include('administracion.preinscripcions.table.carga_csv.fields.hidden')                        
                    @else
                        <span class=" btn badge badge-danger p-1 m-1">NO</span>                        
                    @endif
                </td>
            </tr>

            @endforeach

    </tbody>

</table>

@if ($preinscripcionsCSV->count() > 0)

    <fieldset {{ ( $errors->count() == $preinscripcionsCSV->count()) ? 'disabled':null  }} >
        <div class="btn-group btn-block">
            <button type="submit" class="btn-boletin btn btn-primary w-100">
                <i class="fa fa-save" aria-hidden="true"></i>
                Guardar
            </button>
        </div>
    </fieldset>

@endif

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple_search')


