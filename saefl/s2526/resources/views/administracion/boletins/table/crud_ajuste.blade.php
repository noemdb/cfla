@php
    $class_N="d-none d-sm-table-cell";
    $class_plan="d-none d-md-table-cell";
    $class_descripcion="";
    $class_profesor="d-none d-md-table-cell";
    $class_asignatura="";
    $class_grado="d-none d-lg-table-cell";
    $class_lapso="d-none d-lg-table-cell";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_plan }}">Estudiantes</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_grado }}">Grado</th>
            <th class="{{ $class_grado }}">Sección</th>
            <th class="{{ $class_lapso }}">Lapso</th>
            <th class="{{ $class_lapso }}">Ajuste</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($boletin_ajustes as $boletin_ajuste)

        @php
            $estudiant = $boletin_ajuste->estudiant;
            $pevaluacion = $boletin_ajuste->pevaluacion;
            $seccion = $pevaluacion->seccion;
            $lapso = $pevaluacion->lapso;
            $pensum = $pevaluacion->pensum;
            $asignatura = $pensum->asignatura;
            $grado = $pensum->grado;
        @endphp

        <tr data-id="{{$boletin_ajuste->id ?? null}}" data-evaluacion="{{$boletin_ajuste->id ?? ''}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_plan  ?? ''}}">
                {{ $estudiant->fullname ?? ''}}
                <div class="small">{{$estudiant->ci_estudiant ?? ''}}</div>
            </td>
            <td class="{{ $class_descripcion  ?? ''}}">
                {{ $asignatura->name ?? ''}}
            </td>
            <td class="{{ $class_profesor  ?? ''}}">
                {{ $grado->name ?? ''}}
            </td>
            <td class="{{ $class_profesor  ?? ''}}">
                {{ $seccion->name ?? ''}}
            </td>
            <td class="{{ $class_asignatura ?? '' }}">
                {{ $lapso->name ?? ''}}
            </td>
            <td class="{{ $class_asignatura ?? '' }}">
                {{ $boletin_ajuste->ajuste ?? ''}}
            </td>

            <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $boletin_ajuste->id }}">
                <div class="btn-group btn-group-sm">
                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$boletin_ajuste->id}}">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>

        </tr>
        @endforeach

    </tbody>

</table>
{{-- {!! Form::open(['route' => ['administracion.boletins.destroy',':BOLETIN_AJUSTE_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/boletins/ajuste_destroy.js") }}"></script> @endsection --}}

{!! Form::open(['route' => ['administracion.boletins.ajuste.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

@include('administracion.datatables.exportBootstrap')
