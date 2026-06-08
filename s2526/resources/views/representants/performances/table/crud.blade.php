@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-sm table-hover p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                {{-- <th class="{{ $class_profesor }}">Profesor</th> --}}
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_lapso }}">Tipo de Nota</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pevaluacions as $pevaluacion)

            @php $pensum = $pevaluacion->pensum; $grado = $pevaluacion->pensum->grado; $pensum = $pevaluacion->pensum; @endphp

            <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}"
            class="table-{{(empty($pevaluacion->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                {{-- <td id="td-pevaluacion-profesor-{{ $pevaluacion->id }}" class="{{ $class_user  ?? ''}}">
                    {{ $pevaluacion->profesor->fullname ?? ''}}
                </td> --}}
                <td id="td-pevaluacion-asignatura-{{ $pevaluacion->id }}" class="{{ $class_email ?? '' }}">
                    {{ $pevaluacion->pensum->asignatura->name ?? ''}}
                </td>
                <td id="td-grado-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }} {{$grado->class_text_color}}">
                    {{ $grado->name ?? ''}} {{ $pevaluacion->seccion->name ?? ''}}
                </td>
                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->lapso->name ?? ''}}
                </td>
                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->nota_type ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $pevaluacion->id }}">
                    <div class="btn-group">
                        @php $disabled  = ($pevaluacion->evaluacions->isNotEmpty()) ? ' disabled ': null ; @endphp
                        @php $class_btn = ($pevaluacion->evaluacions->isNotEmpty()) ? 'btn-outline-warning' : 'btn-warning' ; @endphp
                        <a title="Editar" class="btn {{$class_btn ?? ''}} {{$disabled ?? ''}}"  href="{{route('representants.pevaluacions.edit',$pevaluacion->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        @php $disabled  = ($pevaluacion->evaluacions->isEmpty()) ? ' disabled ': null ; @endphp
                        @php $class_btn = ($pevaluacion->evaluacions->isEmpty()) ? 'btn-outline-secondary' : 'btn-success' ; @endphp
                        <a title="Exportar las evaluaciones de éste plan y asignarlas a otra sección" class="btn-clone btn {{$class_btn ?? ''}} {{$disabled ?? ''}}" href="{{route('representants.evaluacions.create_clone',$pevaluacion->id)}}" role="button">
                            <i class="{{ $icon_menus['clone'] ?? ''}} fa-1x"></i>
                        </a>

                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="{{ $icon_menus['options'] ?? ''}} fa-1x"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                            <a title="Registrar Evaluaciones" class="dropdown-item" href="{{route('representants.evaluacions.create',$pevaluacion->id)}}" role="button">
                                <i class="{{ $icon_menus['evaluacion'] ?? ''}} fa-1x text-info"></i>
                                Registrar Evaluaciones
                            </a>

                            @php $disabled = ($pevaluacion->status_eva_complete) ? 'btn-success' : 'btn-outline-secondary' ; @endphp
                            <a title="Registro de Notas" class="dropdown-item" href="{{route('representants.boletins.carga',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                                <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x text-primary"></i>
                                Registro de Notas
                            </a>
                        </div>

                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

{{-- </div> --}}

{{-- {!! Form::open(['route' => ['representants.pevaluacions.store_clone',':PEVALUCION_ID'], 'method' => 'POST', 'id'=>'form-clone', 'role'=>'form']) !!} --}}
{{-- {!! Form::close() !!} --}}
{{-- @section('scripts') @parent <script src="{{ asset("js/models/pevaluacions/clone.js") }}"></script> @endsection --}}

{!! Form::open(['route' => ['representants.pevaluacions.destroy',':PEVALUCION_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/pevaluacions/destroy.js") }}"></script> @endsection

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
