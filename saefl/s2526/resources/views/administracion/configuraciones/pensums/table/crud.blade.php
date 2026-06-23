@php
    $class_N="d-none d-sm-table-cell";
    $class_pestudio="";
    $class_grado="";
    $class_asignatura="";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_pestudio }}">Plan de Estudio</th>
                <th class="{{ $class_grado }}">Grado</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_asignatura }}">PE Asignación</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">

        @foreach($pensums as $pensum)

            <tr data-id="{{$pensum->id}}" data-pensum="{{$pensum->id ?? ''}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-pensum-pestudio-{{ $pensum->id }}" class="{{ $class_user  ?? ''}}">
                    {{ $pensum->pestudio->name ?? ''}}
                </td>
                <td id="td-pensum-grado-{{ $pensum->id }}" class="{{ $class_email ?? '' }}">
                    {{ $pensum->grado->name ?? ''}}
                </td>

                <td id="td-asignatura-{{ $pensum->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pensum->asignatura->name ?? ''}}
                </td>

                <td id="td-pe-asignacion-{{ $pensum->id }}" class="{{ $class_state ?? '' }}" class="text-nowrap">

                    <div class="btn-group btn-group-sm" role="group">
                    @foreach ( $lapsos as $lapso)
                        @php
                            $grado = $pensum->grado;
                            $seccions = ($grado) ? $grado->getSeccionsActive() : null;
                        @endphp
                        @foreach ( $seccions as $seccion)

                            @if ( $pensum->pevaluacions->where('seccion_id', $seccion->id)->where('lapso_id', $lapso->id)->isEmpty())
                                @php $class_btn = 'btn-outline-'.$lapso->color.' text-dark' ; @endphp
                            @else
                                @php $class_btn = 'btn-'.$lapso->color; @endphp
                            @endif

                            <a title="Asignación del Plan de Evaluación de cada asignatura: Sección {{$seccion->name ?? ''}} Lapso: {{$lapso->name}}"
                                class="btn btn-sm {{$class_btn ?? ''}} text-light "
                                {{-- href="#" --}}
                                {{-- href="{{ route('administracion.pevaluacions.carga',['grado_id'=>$pensum->grado->id,'seccion_id'=>$seccion->id]) }}" --}}
                                >
                                {{$seccion->name.$lapso->id ?? ''}}
                            </a>
                        @endforeach
                    @endforeach
                    </div>

                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $pensum->id }}">

                    <div class="btn-group btn-group-sm">

                        @php $disabled = (!empty($pensum->pevaluacions->count())) ? ' disabled ': null ; @endphp

                        <a title="Editar" class="btn btn-warning btn-sm" href="{{route('administracion.configuraciones.pensums.edit',$pensum->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$pensum->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>

                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.configuraciones.pensums.destroy',':PENSUM_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/pensums/destroy.js") }}"></script> @endsection

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
