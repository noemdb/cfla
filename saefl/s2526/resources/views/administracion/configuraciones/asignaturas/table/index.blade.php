@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_code_sm }}">P.Estudio</th>
            <th class="{{ $class_code_sm }}">Abreviación</th>
            <th class="{{ $class_asignatura }}">Código</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th title="Horas Teóricas" class="{{ $class_ht }}">H.Teóricas</th>
            <th title="Horas Prácticas" class="{{ $class_hp }}">H.Prácticas</th>
            <th title="Índice Académico" class="{{ $class_hp }}">IA</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($asignaturas as $asignatura)

    @php $pestudio = $asignatura->pestudio; @endphp

        <tr data-id="{{$asignatura->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_code_sm  ?? ''}}">
                {{ ($pestudio) ? $pestudio->code : null }}
            </td>
            </td>
            <td class="{{ $class_code_sm  ?? ''}}">
                {{$asignatura->code_sm}}
            </td>
            <td class="{{ $class_user  ?? ''}}">
                {{$asignatura->code}}
            </td>
            <td class="{{ $class_user  ?? ''}}">
                {{$asignatura->name}}
            </td>
            <td class="{{ $class_ht  ?? ''}}">
                {{$asignatura->hour_t_week}}
            </td>
            <td class="{{ $class_hp  ?? ''}}">
                {{$asignatura->hour_p_week}}
            </td>
            <td  class="{{ $class_hp  ?? ''}}">
                {{($asignatura->enable_academic_index=="true") ? 'SI' : 'NO'}}
            </td>
            <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $asignatura->id }}">
                <div class="btn-group btn-group-sm">
                    @php $id_modal = 'modal_show_'.$asignatura->id; @endphp
                    <a title="Mostrar" class="btn btn-info" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                        <i class="{{ $icon_menus['show'] ?? ''}}" aria-hidden="true"></i>
                    </a>
                    @component('elements.widgets.modal')
                        @slot('classH','secondary')
                        @slot('id',$id_modal)
                        @slot('title','Detalles de la Asignatura')
                        @slot('close',true)
                        @slot('size','modal-lg')
                        @slot('body')
                            @include('administracion.configuraciones.asignaturas.partials.details')
                        @endslot
                    @endcomponent

                    <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.configuraciones.asignaturas.edit',$asignatura->id)}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>

                    @php $disabled = ($asignatura->pensums->isNotEmpty()) ? ' disabled ': null ; @endphp
                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs {{$disabled}}" href="#" id="btn-destroy_id_{{$asignatura->id}}">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.configuraciones.grados.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/default/destroy.js") }}"></script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
