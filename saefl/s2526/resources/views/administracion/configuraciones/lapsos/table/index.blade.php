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
            <th class="{{ $class_asignatura }}">Código</th>
            <th class="{{ $class_code_sm }}">Abreviación</th>
            <th class="{{ $class_code_sm }}">Nombre</th>
            <th class="{{ $class_ht }}">Inicio/Fin</th>
            <th class="{{ $class_ht }}">Fecha de Corte de Notas</th>
            <th class="{{ $class_ht }}">Censo</th>
            <th class="{{ $class_ht }}">Precierre</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($lapsos as $lapso)

        <tr data-id="{{$lapso->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_asignatura  ?? ''}}">
                {{$lapso->code ?? ''}}
            </td>
            <td class="{{ $class_code_sm  ?? ''}}">
                {{$lapso->code_sm ?? ''}}
            </td>
            <td class="{{ $class_code_sm  ?? ''}}">
                {{$lapso->name ?? ''}}
            </td>
            <td class="{{ $class_ht  ?? ''}}">
                {{ f_date($lapso->finicial) ?? ''}} / {{ f_date($lapso->ffinal) ?? ''}}
            </td>
            
            <td class="{{ $class_ht  ?? ''}}">
                {{ f_date($lapso->date_cutnote) ?? ''}}
            </td>

            <td class="{{ $class_ht  ?? ''}}">
                {{ f_date($lapso->date_start_census) ?? ''}} / {{ f_date($lapso->date_end_census) ?? ''}}
            </td>
            <td class="{{ $class_ht  ?? ''}}">
                {{ ($lapso->full_date_preclosing) ? $lapso->full_date_preclosing->format('d-m-Y h:ia') : null}}
            </td>
            <td class="{{ $class_action ?? '' }}">
                <div class="btn-group btn-group-sm">

                    @php $id_modal = 'modal_show_'.$lapso->id; @endphp
                    <a title="Mostrar" class="btn btn-info" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                        <i class="{{ $icon_menus['show'] ?? ''}}" aria-hidden="true"></i>
                    </a>
                    @component('elements.widgets.modal')
                        @slot('classH','secondary')
                        @slot('id',$id_modal)
                        @slot('title','Detalles del Lapso')
                        @slot('close',true)
                        @slot('size','modal-md')
                        @slot('body')
                            @include('administracion.configuraciones.lapsos.partials.details')
                        @endslot
                    @endcomponent

                    @php $id_modal = 'modal_edit_'.$lapso->id; @endphp
                    <a title="Editar" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}" href="#" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>
                    @component('elements.widgets.modal')
                        @slot('classH','secondary')
                        @slot('id',$id_modal)
                        @slot('title','Actualizar Lapso')
                        @slot('close',true)
                        @slot('size','modal-lg')
                        @slot('body')
                        @include('administracion.configuraciones.lapsos.partials.edit')
                        @endslot
                    @endcomponent

                    @php $disabled = ($lapso->status_delete) ? null:'disabled'; @endphp
                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{$disabled ?? ''}}" href="#" id="btn-destroy_id_{{$lapso->id}}">
                        <i class="fas fa-trash"></i>
                    </a>

                    <a title="Listado de participantes" class="btn-info btn btn-info btn-sm" href="{{route('administracion.configuraciones.lapsos.census.index',$lapso->id)}}">
                        <i class="{{ $icon_menus['census'] ?? ''}} fa-1x"></i>
                    </a>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.configuraciones.lapsos.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
