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
                <th class="{{ $class_asignatura }}">Plan de Estudio</th>
                <th class="{{ $class_profesor }}">Estudiante</th>
                <th class="{{ $class_asignatura }}">Grado</th>
                <th class="{{ $class_asignatura }}">Seccion</th>
                <th class="{{ $class_asignatura }}">Serial</th>
                <th class="{{ $class_lapso }}">Observaciones</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($titulos as $titulo)

                @php
                    $registro_titulo = $titulo->registro_titulo;
                    $pestudio = $registro_titulo->pestudio;
                    $estudiant = $titulo->estudiant;
                    $seccion = $titulo->seccion;
                @endphp

                <tr data-id="{{$titulo->id}}">
                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>
                    <td  class="{{ $class_asignatura  ?? ''}}">
                        {{ $pestudio->name ?? ''}}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $estudiant->fullname ?? ''}}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $seccion->grado->name ?? ''}}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $seccion->name ?? ''}}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $titulo->serie ?? ''}}
                    </td>

                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $titulo->observations ?? ''}}
                    </td>

                    <td class="{{ $class_action ?? '' }}">

                        <div class="btn-group btn-group-sm">

                            {{-- <a title="Editar" class="btn-edit btn btn-warning btn-sm"  href="#" role="button"> --}}
                                {{-- <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i> --}}
                            {{-- </a> --}}

                            {{-- @admin --}}
                            <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy">
                                <i class="fas fa-trash"></i>
                            </a>
                            {{-- @endadmin --}}

                        </div>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.titulos.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
