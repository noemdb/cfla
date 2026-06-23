@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Nombre</th>
            <th class="{{ $class_ci }}">Descripción</th>
            <th class="{{ $class_grado }}">N.Planes Benéficos</th>
            {{-- <th class="{{ $class_grado }}">N. Estudiantes</th> --}}
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>
    <tbody id="tdatos">
        @foreach($descuentos as $descuento)
            <tr data-id="{{$descuento->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{ $descuento->descuento_name ?? ''}}
                </td>
                <td class="{{ $class_email ?? '' }}">
                    {{ $descuento->descuento_description ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ (!empty($descuento->plan_beneficos)) ? $descuento->plan_beneficos->count():null}}
                </td>
                {{-- <td class="{{ $class_state ?? '' }}"> --}}
                    {{-- {{ (!empty($descuento->estudiants)) ? $descuento->estudiants->count():null}} --}}
                {{-- </td> --}}
                <td class="{{ $class_action ?? '' }}">
                    @php $disabled = ($descuento->status_delete) ? null:'disabled'; @endphp
                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$descuento->id}}">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>

</table>

{!! Form::open(['route' => ['administracion.configuraciones.descuentos.destroy',':DESCUENTO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':DESCUENTO_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/default/destroy.js") }}"></script>
@endsection


{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
