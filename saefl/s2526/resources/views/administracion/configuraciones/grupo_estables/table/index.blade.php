@php
    $class_N="d-none d-sm-table-cell";
    $class_grupo_estable="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_code_sm }}">Abreviación</th>
            <th class="{{ $class_grupo_estable }}">Código</th>
            <th class="{{ $class_grupo_estable }}">Grupo Estable</th>
            <th title="Horas Teóricas" class="{{ $class_ht }}">H.Teóricas</th>
            <th title="Horas Prácticas" class="{{ $class_hp }}">H.Prácticas</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($grupo_estables as $grupo_estable)

        <tr data-id="{{$grupo_estable->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td id="td-users-username-{{ $grupo_estable->id }}" class="{{ $class_code_sm  ?? ''}}">
                {{$grupo_estable->code_sm}}
            </td>
            <td id="td-users-username-{{ $grupo_estable->id }}" class="{{ $class_user  ?? ''}}">
                {{$grupo_estable->code}}
            </td>
            <td id="td-users-username-{{ $grupo_estable->id }}" class="{{ $class_user  ?? ''}}">
                {{$grupo_estable->name}}
            </td>
            <td id="td-users-username-{{ $grupo_estable->id }}" class="{{ $class_ht  ?? ''}}">
                {{$grupo_estable->hour_t_week}}
            </td>
            <td id="td-users-username-{{ $grupo_estable->id }}" class="{{ $class_hp  ?? ''}}">
                {{$grupo_estable->hour_p_week}}
            </td>
            <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $grupo_estable->id }}">
                <div class="btn-group btn-group-sm">
                    @php $id_modal = 'modal_show_'.$grupo_estable->id; @endphp
                    <a title="Mostrar" class="btn btn-info" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                        <i class="{{ $icon_menus['show'] ?? ''}}" aria-hidden="true"></i>
                    </a>
                    @component('elements.widgets.modal')
                        @slot('classH','secondary')
                        @slot('id',$id_modal)
                        @slot('title','Detalles del Grupo Estable')
                        @slot('close',true)
                        @slot('size','modal-lg')
                        @slot('body')
                            @include('administracion.configuraciones.grupo_estables.partials.details')
                        @endslot
                    @endcomponent
                    <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.configuraciones.grupo_estables.edit',$grupo_estable->id)}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>
                    @php $disabled = ($grupo_estable->status_delete) ? null : ' disabled ' ; @endphp
                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$grupo_estable->id}}">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.configuraciones.grupo_estables.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/default/destroy.js") }}"></script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
