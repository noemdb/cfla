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
                <th class="{{ $class_asignatura }}">Código Oficial</th>
                <th class="{{ $class_code_sm }}">Nombre</th>
                <th class="{{ $class_code_sm }}">Programa de Estudio</th>
                <th class="{{ $class_asignatura }}">Descripción</th>
                <th class="{{ $class_ht }}">Estado</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pestudios as $pestudio)

            <tr data-id="{{$pestudio->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$pestudio->code ?? ''}}
                </td>
                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$pestudio->code_oficial ?? ''}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{$pestudio->name ?? ''}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{$pestudio->peducativo->name ?? ''}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{$pestudio->description ?? ''}}
                </td>
                <td class="{{ $class_ht  ?? ''}}">
                    {{($pestudio->status_active=='true') ? 'Activo':'Desactivo'}}
                </td>
                <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group btn-group-sm">

                        @php $id_modal = 'modal_edit_'.$pestudio->id; @endphp
                        <a title="Mostrar Plan de Estudio" class="btn btn-info" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                            <i class="{{ $icon_menus['show'] ?? ''}}" aria-hidden="true"></i>
                        </a>
                        @component('elements.widgets.modal')
                            @slot('classH','secondary')
                            @slot('id',$id_modal)
                            @slot('title','Mostrar Plan de Estudio')
                            @slot('close',true)
                            @slot('size','modal-lg')
                            @slot('body')
                                @include('administracion.configuraciones.pestudios.show.details')
                            @endslot
                        @endcomponent

                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.configuraciones.pestudios.edit',$pestudio->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        @php $disabled = ($pestudio->status_delete) ? null:'disabled'; @endphp
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{$disabled ?? ''}}" href="#" id="btn-destroy_id_{{$pestudio->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach

        </tbody>

    </table>

{{-- </div> --}}

{!! Form::open(['route' => ['administracion.configuraciones.pestudios.destroy',':PESTUDIO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':PESTUDIO_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/default/destroy.js") }}"></script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
