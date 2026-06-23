@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_profesor }}">Institución</th>
                <th class="{{ $class_asignatura }}">Plan de Estudio</th>
                <th class="{{ $class_profesor }}">Nombre</th>
                <th class="{{ $class_asignatura }}">F.Egreso</th>
                <th class="{{ $class_asignatura }}">Código</th>
                <th class="{{ $class_lapso }}">Tipo de Registro</th>
                <th class="{{ $class_lapso }}">Tipo de evaluacion</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($registro_titulos as $registro_titulo)

                @php
                    $institucion = $registro_titulo->institucion;
                    $pestudio = $registro_titulo->pestudio;
                @endphp

                <tr data-id="{{$registro_titulo->id}}">
                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>
                    <td class="{{ $class_profesor  ?? ''}}">
                        {{ $institucion->name ?? ''}}
                    </td>
                    <td  class="{{ $class_asignatura  ?? ''}}">
                        {{ $pestudio->name ?? ''}}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $registro_titulo->name ?? ''}}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ f_date($registro_titulo->fecha_egreso) }}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $registro_titulo->code ?? ''}}
                    </td>

                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $registro_titulo->tipo ?? ''}}
                    </td>

                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $registro_titulo->tevaluacion->name ?? ''}}
                    </td>

                    <td class="{{ $class_action ?? '' }}">
                        <div class="btn-group btn-group-sm">

                            <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.registro_titulos.edit',$registro_titulo->id)}}" role="button">
                                <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                            </a>

                            @control
                            @php $status_delete = ($registro_titulo->status_delete) ? null : 'disabled'; @endphp
                            <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{$status_delete ?? ''}}" href="#" id="btn-destroy">
                                <i class="fas fa-trash"></i>
                            </a>
                            @endcontrol
                        </div>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.registro_titulos.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script>@endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
