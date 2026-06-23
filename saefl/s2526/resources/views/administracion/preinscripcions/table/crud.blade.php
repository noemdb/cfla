@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_planpago }}">Grado</th>
                <th class="{{ $class_planpago }}">Grupo Estable</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)

                @php
                    $preinscripcion = $estudiant->preinscripcion;
                    $seccion = $preinscripcion->seccion;
                    $grado = $preinscripcion->grado;
                @endphp

                <tr data-id="{{$preinscripcion->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->fullname ?? ''}}
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>
                    <td class="{{ $class_ci ?? '' }}">
                        {{ $grado->name ?? ''}}
                    </td>
                    <td class="{{ $class_ci ?? '' }}">
                        {{ (!empty($preinscripcion->grupo_estable)) ? $preinscripcion->grupo_estable->code : $preinscripcion->data_ge_name }}
                    </td>

                </tr>
            @endforeach

        </tbody>
    </table>
    {!! Form::open(['route' => ['administracion.preinscripcions.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
    {!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
    {!! Form::close() !!}
    @section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

    <div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')

