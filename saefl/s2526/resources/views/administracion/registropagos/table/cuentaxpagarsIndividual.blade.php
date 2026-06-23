@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="d-none";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp


<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_ci }}">Identificador</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            <th class="{{ $class_fecha }}">Concepto</th>
            <th class="{{ $class_fecha }}">F.Vencimiento</th>
            <th class="{{ $class_fecha }}">Monto[$]</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($datas as $data)

            @php
            $estudiant = $data['estudiant'];
            $representant = $data['representant'];
            $exchange_ammount = $data['exchange_ammount'];
            $concepto = $data['concepto'];
            $date_expiration = $data['date_expiration'];
            @endphp

            <tr data-estudiant_id="{{$estudiant->id}}">
                <td class="{{ $class_N }}">{{$loop->iteration}} - {{$data['id']}}</td>
                <td class="{{ $class_ci }}">
                    <span class=" font-weight-bold"> {{$estudiant->ci_estudiant ?? ''}} </span>
                </td>
                <td class="{{ $class_estudiant }}">
                    <span class=" font-weight-bold"> {{$estudiant->fullname ?? ''}} </span>
                </td>
                <td class="{{ $class_fecha }}">
                    <span class=" text-muted font-weight-bold"> {{ $concepto ?? ''}} </span>
                </td>
                <td class="{{ $class_fecha }}">
                    <span class=" text-muted font-weight-bold"> {{ $date_expiration ?? ''}} </span>
                </td>
                <td class="{{ $class_fecha }}">
                    <span class=" font-weight-bold"> {{ round($exchange_ammount,2) ?? ''}} </span>
                </td>
            </tr>

        @endforeach

    </tbody>
</table>

<div id="container_modal"></div>

@section('scripts')
    @parent
    <script>
        $('.btn-nodal-historico').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('representant_id');  //console.log(id);
            var modal = '#modal_historico';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.representant_historico_pago", "_id_")}}';
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
    <script>
        $('.btn-card').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('dd'); console.log(row)//fila contentiva de la data
            var id = row.data('id');  console.log(id);
            var modal = '#modal_card';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.estudiant_card", "_id_")}}';
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

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')
