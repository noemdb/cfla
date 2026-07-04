@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_fecha="d-none d-md-table-cell";
    $class_banco="d-none d-md-table-cell";
    $class_monto="d-none d-lg-table-cell";
    $class_action="nosort text-center";
@endphp

<table width="100%" class="table table-hover  table-sm small p-1" id="table-data-default"> {{-- table-hover table-striped --}}

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_fecha }} text-nowrap" title="Fecha Registro">F. Registro</th>
            <th class="{{ $class_fecha }}" title="Fecha Operación">F. Oper.</th>
            <th class="{{ $class_banco }} text-nowrap">Banco</th>
            <th class="{{ $class_monto }} text-nowrap">Referencia</th>
            <th class="{{ $class_monto }} text-nowrap">Monto Bs.</th>
            <th class="{{ $class_monto }} text-wrap">Comentario</th>
            <th class="{{ $class_monto }} text-nowrap text-center">Aprobada</th>
            <th class="{{ $class_monto }} text-nowrap text-center">Estado</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

    @foreach($prepagos as $prepago)

        @php
        $representant = $prepago->representant;
        $status_approved = $prepago->status_approved;
        $status_apply = $prepago->status_apply;
        @endphp

        <tr data-id="{{$prepago->id ?? ''}}" class="alert-{{ ($status_apply=="true") ? 'success text-muted':null }}" id="row_prepago_{{$prepago->id}}">
            <td id="td-id" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_fecha ?? '' }} text-nowrap">
                {{ $prepago->created_at->format('d-m-Y') }}
            </td>
            <td class="{{ $class_fecha ?? '' }} text-nowrap">
                {{ f_date($prepago->date_transaction) }}
            </td>
            <td class="{{ $class_monto ?? '' }} text-nowrap">
                {{ $prepago->banco->name ?? ''}}
            </td>
            <td class="font-weight-bold {{ $class_monto ?? '' }} text-nowrap">
                {{ $prepago->number_i_pay ?? ''}} {{(!empty($prepago->deleted_at))? '[BORRADO]':'' }}
            </td>
            <td class="{{ $class_monto ?? '' }} text-nowrap">
                {{ (!empty($prepago->ingreso_ammount)) ? f_float($prepago->ingreso_ammount):''}}
            </td>
            <td class="{{ $class_monto ?? '' }} text-wrap">
                {{ $prepago->comment ?? ''}}
            </td>
            <td class="{{ $class_monto ?? '' }} text-nowrap text-center">
                @switch($status_approved)
                    @case("true") <span class="badge badge-success">APROBADA</span>@break
                    @case("false") <span class="badge badge-danger">RECHAZADA</span>@break
                    @default <span class="badge badge-info">EN REVISIÓN</span>
                @endswitch
            </td>
            <td class="{{ $class_monto ?? '' }} text-nowrap text-center">
                <span class="badge badge-{{ ($status_apply=="true") ? 'success':'warning' }}">
                    {{ ($status_apply=="true") ? 'APLICADA':'NO APLICADA' }}
                </span>
            </td>
            <td class="{{ $class_action ?? '' }}">
                <fieldset {{ ($status_approved==null && $status_apply==null) ? null:'disabled' }}>
                    <div class="btn-group btn-group-sm">

                        <button title="Eliminar notificación"  type="button" class="btn-destroy btn btn-danger btn-sm">
                            <i class="{{ $icon_menus['eliminar'] }} fa-1x text-light "></i>
                        </button>

                    </div>
                </fieldset>
            </td>
        </tr>

    @endforeach

    </tbody>
</table>

{!! Form::open(['route' => ['representants.prepagos.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables (default,simple,simple_search) --}}
@include('representants.datatables.default')
