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
                <th class="{{ $class_asignatura }}">Estudiante</th>
                <th class="{{ $class_code_sm }}">Lapso</th>
                <th class="{{ $class_code_sm }}">Nombre</th>
                <th class="{{ $class_ht }}">Descripción</th>
                <th class="{{ $class_ht }}">Observación</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($edescriptivas as $edescriptiva)

            @php $estudiant = $edescriptiva->estudiant @endphp
            @php $lapso = (!empty($edescriptiva->lapso)) ? $edescriptiva->lapso:'Final'; @endphp

            <tr data-id="{{$edescriptiva->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_asignatura  ?? ''}}">
                    {{$estudiant->fullname ?? ''}} [{{$estudiant->ci ?? ''}}]
                </td>
                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$edescriptiva->lapso->name ?? 'Final'}}
                </td>

                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$edescriptiva->name ?? ''}}
                </td>

                <td class="{{ $class_ht  ?? ''}}">
                    {{$edescriptiva->description ?? ''}}
                </td>

                <td class="{{ $class_ht  ?? ''}}">
                    {{$edescriptiva->description ?? ''}}
                </td>

                <td class="{{ $class_ht  ?? ''}}">
                    {{$edescriptiva->observations ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }}">

                    <div class="btn-group btn-group-sm">

                        @php $id_modal = 'modal_show_'.$edescriptiva->id; @endphp
                        <a title="Mostrar Plan de Estudio" class="btn btn-info" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                            <i class="{{ $icon_menus['show'] ?? ''}}" aria-hidden="true"></i>
                        </a>
                        @component('elements.widgets.modal')
                            @slot('classH','secondary')
                            @slot('id',$id_modal)
                            @slot('title','Detalles de la evalación descriptiva')
                            @slot('close',true)
                            @slot('size','modal-md')
                            @slot('body')
                                {{-- @include('administracion.edescriptivas.partials.details') --}}
                            @endslot
                        @endcomponent

                        @php $id_modal = 'modal_edit_'.$edescriptiva->id; @endphp
                        <a title="Editar" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}" href="#" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        @component('elements.widgets.modal')
                            @slot('classH','secondary')
                            @slot('id',$id_modal)
                            @slot('title','Actualizar Evaluación')
                            @slot('close',true)
                            @slot('size','modal-md')
                            @slot('body')
                            {{-- @include('administracion.edescriptivas.partials.edit') --}}
                            @endslot
                        @endcomponent

                        @php $disabled = ($edescriptiva->status_delete) ? null:'disabled'; @endphp
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{$disabled ?? ''}}" href="#" id="btn-destroy_id_{{$edescriptiva->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>
                </td>
            </tr>
        @endforeach

        </tbody>

    </table>

{{-- </div> --}}

{!! Form::open(['route' => ['administracion.edescriptivas.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
