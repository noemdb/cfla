@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_fecha="d-none d-md-table-cell";
    $class_banco="d-none d-md-table-cell";
    $class_monto="d-none d-lg-table-cell";
    $class_action="text-center";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"> {{-- table-hover  --}}

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_representant }}">Representante</th>
            <th class="{{ $class_fecha }} text-nowrap" title="Fecha Registro">F. Registro</th>
            <th class="{{ $class_fecha }}" title="Fecha Operación">F. Operación</th>
            <th class="{{ $class_banco }} text-nowrap">Banco</th>
            <th class="{{ $class_monto }} text-nowrap">Referencia</th>
            <th class="{{ $class_monto }} text-nowrap">Monto Bs.</th>
            <th class="{{ $class_monto }} text-wrap">Comentario</th>
            <th class="{{ $class_monto }} text-nowrap text-center">Aprobación</th>
            <th class="{{ $class_monto }} text-nowrap">Estado</th>
            <th class="{{ $class_monto }} text-nowrap">Saldo Bs.</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($prepagos as $prepago)

            @php
            $representant = $prepago->representant;
            $status_approved = $prepago->status_approved;
            $status_apply = $prepago->status_apply;
            $status_enable = $prepago->status_enable;
            @endphp

            <tr data-id="{{$prepago->id ?? ''}}" class="table-{{ ($status_apply=="true") ? 'secondary text-muted':null }}" id="row_prepago_{{$prepago->id}}">
                <td id="td-id" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_representant  ?? ''}}">
                    @if ($representant)
                        <b>{{$representant->name ?? ''}}</b>
                        <br>
                        <span class="text-muted">
                            ({{ $representant->ci_representant ?? ''}})
                        </span>
                    @endif                
                </td>
                <td class="{{ $class_fecha ?? '' }} text-nowrap">
                    {{ f_date($prepago->created_at->format('d-m-Y')) }}
                </td>
                <td class="{{ $class_fecha ?? '' }} text-nowrap">
                    {{ f_date($prepago->date_transaction->format('d-m-Y')) }}
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
                        {{ ($status_apply<>"true") ? 'NO APLICADA':'APLICADA' }}
                    </span>
                </td>
                @php $ammount_expire_bill = ($representant) ? $representant->ammount_expire_bill : null; @endphp
                <td class="{{ $class_monto ?? '' }} text-wrap {{ ($ammount_expire_bill > 0) ? ' font-weight-bold text-danger ':null  }}">
                    {{ f_float($ammount_expire_bill) }}
                </td>

                <td class="{{ $class_action ?? '' }}">

                    <fieldset {{ ($status_apply) ? 'disabled=disabled':null }}>

                        <div class="btn-group btn-group-sm">

                            <a class="btn-abono btn btn-success btn-sm {{ ($status_enable) ? null : 'disabled' }}" href="#" role="button">
                                <i class="{{ $icon_menus['abonos'] }} fa-1x text-light "></i>
                            </a>
                            <a class="btn-pago btn btn-secondary btn-sm {{ ($status_enable) ? null : 'disabled' }}" href="#" role="button">
                                <i class="{{ $icon_menus['registro_pagos'] }} fa-1x text-light "></i>
                            </a>

                        </div>

                    </fieldset>

                </td>

            </tr>

        @endforeach

    </tbody>
    
</table>

<div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables (default,simple,simple_search) --}}
@include('administracion.datatables.default')

@section('scripts')
    @parent
    <script>

        $('.btn-abono').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_abono_'+id;  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.prepago.abono", "_id_")}}';
            ajaxurl = ajaxurl.replace('_id_', id);

            var $this = $(this);
            var loadingText = '<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>';
            $this.data('original-text', $(this).html());
            $this.html(loadingText);

            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    $this.html($this.data('original-text'));
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });

        $('.btn-pago').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_pago_'+id;  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.prepago.pago", "_id_")}}';
            ajaxurl = ajaxurl.replace('_id_', id);

            var $this = $(this);
            var loadingText = '<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>';
            $this.data('original-text', $(this).html());
            $this.html(loadingText);

            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    $this.html($this.data('original-text'));
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });

    </script>
@endsection
