@php 
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp 

    <table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_action }}">Asignar</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($estudiants as $estudiant)
            @php
                $enable_inscription = $estudiant->enable_inscription;
                $status_administrativa = empty($estudiant->administrativa->id);
            @endphp

            <tr data-estudiant="{{$estudiant->id}}" data-estudiant="{{$estudiant->id ?? ''}}" class="table-{{(empty($estudiant->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user  ?? ''}}">
                    <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                        <span class=" font-weight-bold text-{{ ($enable_inscription) ? 'dark':'danger'}}">{{$estudiant->fullname}}</span>
                    </a>
                </td>
                <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_email ?? '' }}">
                    <span class="text-profiles-ci_estudiant-{{ $estudiant->id ?? ''}}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </span>
                </td>

                <td id="td-users-is_active-{{ $estudiant->id }}" class="{{ $class_state ?? '' }}">
                    <span class="text-users-is_active-{{ $estudiant->id }} text-{{ $estudiant->is_active }}">
                        {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                    </span>
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                    @php
                        $check_status = "";
                        $disabled='';
                        $title='';
                    @endphp                        
                    {{-- @if ($estudiant->planpago_id == $planpago->id) --}}
                    @if ($estudiant->planpago_id == $planpago->id && !$status_administrativa)
                        @php
                            $check_status = "true";                            
                        @endphp                     
                    @endif
                    @if ( !$status_administrativa || !$enable_inscription )
                        @php
                            $disabled = "disabled";
                        @endphp
                        @if ( !$status_administrativa )
                            @php
                                $title = "Fecha de asignación de plan de pago: ".f_date($estudiant->administrativa->created_at)
                            @endphp    
                        @endif                                             
                    @endif
                    {{-- {{$status_administrativa ?? ''}} {{$estudiant->enable_inscription ?? ''}} --}}
                    <div class="btn-group btn-group align-content-center" title="{{$title ?? ''}}">
                        @component('administracion.elements.forms.check')
                            @slot('name', 'arr_planpago['.$estudiant->id.']')
                            @slot('id', 'arr_planpago_id_'.$estudiant->id)
                            @slot('value', $check_status)
                            @slot('disabled', $disabled)
                        @endcomponent

                        {{-- <div class="form-group form-check">
                            <input type="checkbox" {{$check_status ?? ''}} value="true" class="form-check-input" name="arr_planpago[{{$planpago->id}}][{{$estudiant->id}}]" id="arr_planpago">
                        </div> --}}
                    </div>
                </td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
    
    <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block mt-3" value="Registrar" data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
        <i class="far fa-save"></i>
        Establer Plan
    </button> 

    @section('scripts')
        @parent
        <script type="text/javascript">
            $(document).ready(function() {
                $('.crt_checkboxes').click(function (e) {
                    var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                    var name = div.data('name');  console.log(name);
                    var checked = $(this).prop('checked'); console.log(checked);
                    $('#'+name).val(checked); console.log($('#'.name).val());
                });
            });
        </script>
    @endsection 

{{-- </div> --}}

@section('stylesheet')
    @parent

    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">

@endsection

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