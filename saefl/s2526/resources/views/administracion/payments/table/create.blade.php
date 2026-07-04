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
            <th class="{{ $class_fecha }}" title="Fecha Operación">F. Opereración</th>
            <th class="{{ $class_banco }} text-nowrap">Banco</th>
            <th class="{{ $class_monto }} text-nowrap">Referencia</th>
            <th class="{{ $class_monto }} text-nowrap">Monto Bs.</th>
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
            <td class="{{ $class_action ?? '' }}">
                @php
                    $disabled = ($status_associated || $mbancario->status_apply ) ? ' disabled ':null;
                @endphp
                <fieldset {{ $disabled ?? null }}>
                    <div class="btn-group btn-group-sm">

                        <button title="Eliminar notificación"  type="button" class="btn-create btn btn-primary btn-sm {{ $disabled ?? null }}">
                            <i class="{{ $icon_menus['nuevo'] }} fa-1x text-light "></i>
                        </button>

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
        $('.btn-create').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_create_'+id;  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.prepago.create", "_id_")}}';
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