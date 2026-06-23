@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_profesor }}">Profesor</th>
                <th class="{{ $class_grado }}">Grado</th>
                <th class="{{ $class_grado }}">Sección</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($profesor_guias as $profesor_guia)
            @php
                $class_callout = "bd-callout bd-callout-".$profesor_guia->grado->color;
                $profesor = $profesor_guia->profesor;
                $grado = $profesor_guia->grado;
                $pestudio = $grado->pestudio;
                $lapso = $profesor_guia->lapso;
                $seccion = $profesor_guia->seccion;
            @endphp

            <tr data-id="{{$profesor_guia->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{ $profesor->fullname ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">                    
                    {{ $grado->name ?? ''}}
                    <div class="small text-muted">{{ $pestudio->name ?? ''}}</div>
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ $seccion->name ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ $profesor_guia->lapso->name ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group btn-group-sm">

                        {{-- <a title="Editar" class="btn btn-warning btn-sm disabled"  href="{{route('administracion.configuraciones.profesor_guias.edit',$profesor_guia->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a> --}}

                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$profesor_guia->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.configuraciones.profesor_guias.destroy',':PROFESOR_GUIA_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/profesor_guias/destroy.js") }}"></script> @endsection

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection
