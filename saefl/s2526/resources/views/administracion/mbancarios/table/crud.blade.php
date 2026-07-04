@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_fecha="d-none d-md-table-cell";
    $class_banco="d-none d-md-table-cell";
    $class_monto="d-none d-lg-table-cell";
    $class_action="nosort text-center";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"> {{-- table-hover  --}}

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_fecha }} text-nowrap" title="Fecha Registro">F. Registro</th>
            <th class="{{ $class_fecha }}" title="Fecha Operación">F. Oper.</th>
            <th class="{{ $class_banco }} text-nowrap">Banco</th>
            <th class="{{ $class_monto }} text-nowrap">Referencia</th>
            <th class="{{ $class_monto }} text-nowrap">Monto Bs.</th>
            <th class="{{ $class_monto }} text-nowrap">Estado</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

    @foreach($mbancarios as $mbancario)

        @php 
            $status_associated = $mbancario->status_associated ;
            $status_apply = $mbancario->status_apply;
        @endphp

        <tr data-id="{{$mbancario->id ?? ''}}" class="table-{{ ($status_apply=="true") ? 'secondary text-muted':null }}">
            <td id="td-id" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_fecha ?? '' }} text-nowrap">
                {{ $mbancario->created_at->format('d-m-Y') }}
            </td>
            <td class="{{ $class_fecha ?? '' }} text-nowrap">
                {{ $mbancario->date_transaction->format('d-m-Y') }}
            </td>
            <td class="{{ $class_monto ?? '' }} text-nowrap">
                {{ $mbancario->banco->name ?? ''}}
            </td>
            <td class="font-weight-bold {{ $class_monto ?? '' }} text-nowrap">
                {{ $mbancario->number_i_pay ?? ''}} {{(!empty($mbancario->deleted_at))? '[BORRADO]':'' }}
            </td>
            <td class="{{ $class_monto ?? '' }} text-nowrap">
                {{ (!empty($mbancario->ingreso_ammount)) ? f_float($mbancario->ingreso_ammount):''}}
            </td>
            <td class="{{ $class_monto ?? '' }} text-wrap ">
                <span class="font-weight-bold text-{{ ($status_apply=="true") ? 'danger':'success' }}">
                    {{ ($status_apply=="true") ? 'APLICADO':'DISPONIBLE' }}
                </span>
            </td>
            <td class="{{ $class_action ?? '' }}">
                @php
                    $disabled = ($status_associated || $mbancario->status_apply ) ? ' disabled ':null;
                @endphp
                <fieldset {{ $disabled ?? null }}>
                    <div class="btn-group btn-group-sm">

                        <button title="Eliminar Movimiento Bancario CSV"  type="button" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? null }}">
                            <i class="{{ $icon_menus['eliminar'] }} fa-1x text-light "></i>
                        </button>

                    </div>
                </fieldset>
            </td>
        </tr>

    @endforeach

    </tbody>
</table>

<div id="container_modal"></div>

{!! Form::open(['route' => ['administracion.mbancarios.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables (default,simple,simple_search) --}}
@include('administracion.datatables.default')
