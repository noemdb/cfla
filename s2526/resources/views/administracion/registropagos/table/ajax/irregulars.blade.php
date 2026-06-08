<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-ajax" data-url="{{ url('api/users') }}">
    <thead>
        <tr>
            <th>N°</th>
            <th width="10px" class=" text-nowrap">Representante</th>
            <th>Fecha</th>
            <th>Ingresos[Bs]</th>
            <th>Abonos[Bs]</th>
            <th>CAF[Bs]</th>
            <th>Total[Bs]</th>

            <th>Pagado[Bs]</th>
            <th>CAF[Bs]</th>
            <th>Total[Bs]</th>

            <th>Diferencia[Bs]</th>
            <th width="48px">Acción</th>
        </tr>
    </thead>
</table>

@section('scripts')
    @parent
    <script>
        $('#btn_start').click(function (e) {
            e.preventDefault(); //console.log('123');
            $('#spinner').removeClass('d-none');
            $('#modal_list').modal('toggle');
            $('#count').html('0%');
            $('#fieldset').attr("disabled", "disabled");

            var count = {{ $registro_pago_combinados->count() ?? 0 }}; //console.log(count);
            var n = 0;
            var start = 1;
            var size = count < 12 ? count : 12;
            list_registro_pagos(start,size,count);

            $('#modal_list').modal('toggle');
            $('#fieldset').attr("disabled", "");
        });
    </script>
@endsection

@section('scripts')
    @parent
    <script>

        function list_registro_pagos(start=1,size=12,count=12,n=0)
        {
            var table = '#table-data-ajax'; //console.log(container);
            var ajaxurl = '{{ route("administracion.ajax.api.registro_pagos.irregulars", ["start"=>"_start_","size"=>"_size_"] )}}'; //console.log(ajaxurl);
            ajaxurl = ajaxurl.replace('_start_', start).replace('_size_', size); //console.log(ajaxurl);

            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(response){

                    $.each(JSON.parse(response), function (i, item) {
                        ++n;
                        trHTML(table,n,item);
                    });

                    start = start + size ;
                    if ( start < count ) {
                        size = (start+size) > count ? (count - start) : 12;
                        setTimeout ( list_registro_pagos(start, size, count,n), 60000 );
                    }
                    if (start >= count) {
                        $('#spinner').hide();
                    }
                    var porcentage = (100 * start / count).toFixed(0);
                    $('.count').html(porcentage+'%');
                    $('#start_size_list').html('['+start+' - '+(start+size)+'] - '+count);
                    $('#progress_bar_list').attr('style','width:'+porcentage+'%');

                }
            });
        }

        function trHTML(table,n,item)
        {
            var url = '{{ route("administracion.representants.historico", ["representant_id"=>"_id_"] )}}'.replace('_id_',item.representant_id);
            var btn = $("<a>").attr('href',url).addClass('btn-modal btn btn-light btn-sm').html('<i class="fa fa-history" aria-hidden="true"></i>').attr('title','Histórico de Pagos');
            var btn_fix = $("<a>").attr('href','#').addClass('btn-fix btn btn-light btn-sm').html('<i class="fas fa-check-double"></i>');
            var trHTML = $('<tr>').append(
                $('<td>').text(++n),
                $('<td>').text(item.representant_name+' - '+item.representant_ci).addClass('text-nowrap'),
                $('<td>').text(item.created_at),

                $('<td>').addClass('table-info').text(item.ingreso_ammount),
                $('<td>').addClass('table-info').text(item.abonos_ammount),
                $('<td>').addClass('table-info').text(item.creditos_a_ammount),
                $('<td>').addClass('table-secondary font-weight-bold').text(item.total_creditos),

                $('<td>').addClass('table-success').text(item.pagos_ammount),
                $('<td>').addClass('table-success').text(item.creditos_g_ammount),
                $('<td>').addClass('table-secondary font-weight-bold').text(item.total_debitos),

                $('<td>').addClass('table-danger').text(item.diferencia),
                $('<td>').html(btn)
                // $('<td>').html(btn).append(btn_fix)
            ).attr('data-id',item.id);
            $(table).append(trHTML);
        }

    </script>

@endsection
