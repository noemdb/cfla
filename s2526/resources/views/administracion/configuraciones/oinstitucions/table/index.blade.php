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
                <th class="{{ $class_code_sm }}">Nombre</th>
                <th class="{{ $class_grupo_estable }}">Código</th>
                <th class="{{ $class_code_sm }}">Descripción</th>
                <th class="{{ $class_ht }}">Localización</th>
                <th class="{{ $class_hp }}">Estado - E.F.</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($oinstitucions as $oinstitucion)

            <tr data-id="{{$oinstitucion->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-oinstitucion-name-{{ $oinstitucion->id }}" class="{{ $class_code_sm  ?? ''}}">
                    {{$oinstitucion->name}}
                </td>
                <td id="td-oinstitucion-code-{{ $oinstitucion->id }}" class="{{ $class_grupo_estable  ?? ''}}">
                    {{$oinstitucion->code}}
                </td>
                <td id="td-oinstitucion-description-{{ $oinstitucion->id }}" class="{{ $class_code_sm  ?? ''}}">
                    {{$oinstitucion->description}}
                </td>
                <td id="td-oinstitucion-locations-{{ $oinstitucion->id }}" class="{{ $class_ht  ?? ''}}">
                    {{$oinstitucion->locations}}
                </td>
                <td id="td-oinstitucion-state-{{ $oinstitucion->id }}" class="{{ $class_hp  ?? ''}}">
                    {{$oinstitucion->state}}
                </td>
                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $oinstitucion->id }}">
                    <div class="btn-group btn-group-sm">

                        @php $id_modal = 'modal_show_'.$oinstitucion->id; @endphp                        
                        @include('administracion.configuraciones.oinstitucions.modals.details')
                        
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{ route('administracion.configuraciones.oinstitucions.edit',$oinstitucion->id) }}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        @php $disabled = ($oinstitucion->status_delete) ? null : ' disabled ' ; @endphp
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach

        </tbody>

    </table>

{{-- </div> --}}
{!! Form::open(['route' => ['administracion.configuraciones.oinstitucions.destroy',':OINSTITUCION_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/oinstitucions/destroy.js") }}"></script> @endsection

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
