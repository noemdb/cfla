@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header alert-danger">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    <fieldset id="fieldset">
                        <a name="" id="btn_start" class="btn btn-light" href="#" role="button">
                            <span class="count"></span>
                            <i class="fas fa-play"></i>
                        </a>

                        <a name="" id="btn_auto_fix" class="btn btn-warning" href="#" role="button">
                            <span class="count_fix"></span>
                            <i class="fas fa-check-double"></i>
                        </a>
                    </fieldset>

                    {{-- modal's --}}
                    @include('administracion.registropagos.show.irregulars.modal_wait_list')
                    @include('administracion.registropagos.show.irregulars.modal_wait_fix')

                    @include('administracion.registropagos.menus.crud')

                </div>
                {{-- FIN Menu rapido --}}

                <h4><span title="Listado especial con botones de acción"><u>Listado</u></span> de <span class="font-weight-bolder">Pagos Registrados</span> <span class="text-danger">con irregularidades</span></h4>

            </div>

            <div class="card-body p-2">

                @include('administracion.registropagos.table.ajax.irregulars')

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script>
        $('#btn_auto_fix').click(function (e) {
            e.preventDefault(); //console.log('123');
            var count = {{ $registro_pago_combinados->count() ?? 0 }}; //console.log(count);
            $('#modal_fix').modal('toggle');
            $('#spinner_fix').removeClass('d-none'); //$('#spinner_fix').show();
            $('.count_fix').html('0%');
            $('#fieldset').attr("disabled", "disabled");
            var n = 0;
            var start = 1;
            var size = count < 10 ? count : 10;
            fix_registro_pagos(start,size,count,n);
            $('#modal_fix').modal('toggle');
            $('#fieldset').attr("disabled", "");
        });
    </script>
@endsection

@section('scripts')
    @parent
    <script>
        function fix_registro_pagos(start=1,size=10,count=10,n=0){
            var table = '#table-data-ajax'; //console.log(container);
            var ajaxurl = '{{ route("api_fix_registro_pagos", ["start"=>"_start_","size"=>"_size_"] )}}'; //console.log(ajaxurl);
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
                        size = (start+size) > count ? (count - start) : 10;
                        setTimeout ( fix_registro_pagos(start, size, count,n), 60000 );
                    }
                    if (start >= count) {
                        $('#spinner_fix').hide();
                    }
                    var porcentage = 100 * start / count;
                    // $('#count_fix').html(porcentage.toFixed(0)+'%');
                    var porcentage = (100 * start / count).toFixed(0);
                    $('.count_fix').html(porcentage+'%');
                    $('#start_size_fix').html('['+start+' - '+(start+size)+'] - '+count);
                    $('#progress_bar_fix').attr('style','width:'+porcentage+'%');
                }
            });
        }
    </script>
@endsection


@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - REGISTRO DE PAGOS con irregularidades'; </script> @endsection
