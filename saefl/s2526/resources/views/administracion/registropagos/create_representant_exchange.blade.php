@extends('administracion.layouts.dashboard.app')

@section('title') - Registro de Pagos para Representantes @endsection

@section('main')

<main role="main" id="main" class="col-md-10 ml-sm-auto col-lg-10">

    @php $total_a_pagar = 0; @endphp

    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">

            <h4 class=" py-2">
                <small class="float-right text-right small">
                    @php
                        $exchange_ammount_bill_fg = $representant->exchange_ammount_expire_bill;
                        $exchange_ammount_bill = ($exchange_rate_current) ? $exchange_ammount_bill_fg * $exchange_rate_current->ammount : null;
                        $exchange_rate_current_ammount = ($exchange_rate_current) ? $exchange_rate_current->ammount : null;
                        $registro_pago_combinados = $representant->registro_pago_combinados->sortByDesc('created_at');
                        $exchange_ammount_means = $representant->exchange_ammount_means
                    @endphp

                    @if ($exchange_ammount_bill_fg == 0)
                        <span class="badge badge-success float-right">SOLVENTE</span>
                    @else
                        <small class="small text-muted">Total deuda</small>
                        {{-- <span class="badge badge-danger">{{f_float($representant->ammount_expire_bill)}} Bs.</span> --}}
                        <span class="badge badge-danger mt-1 p-2" title="{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount) : 'Sin tasa de cambio' }}">
                            {{ ($exchange_rate_current) ? 'Bs '. f_float($exchange_ammount_bill) : 'STDC' }}
                        </span>
                        <span class="badge badge-dark">$ {{f_float($exchange_ammount_bill_fg)}}</span>
                        <span class="badge badge-light" title="Recursos disponibles (CAF + ABN)">$ {{f_float($exchange_ammount_means)}}</span>
                    @endif
                    <br>
                </small>

                Registro de Pagos para Representantes

            </h4>

            @include('administracion.representants.show.modal.historico') {{-- Modal --}}

        </div>

        <div class="card-body pt-2">

            @include('administracion.elements.forms.errors')

            <div class="alert-error alert-danger alert-dismissible" role="alert"></div>

            @include('administracion.elements.messeges.oper_ok')

            @include('administracion.registropagos.form.parcial.helt_representant')

            <div class=" pt-2 pb-2 border border-top-0 rounded">

                {!!Form::open(['route'=>'administracion.registropagos.store.representant_exchange','method'=>'POST','id'=>'form-registropago-create-representant','class'=>'form-signin'])!!}

                {{Form::hidden('representant_id',$representant->id)}}
                {{Form::hidden('estudiant_id',$representant->estudiants->first()->id)}}
                {{Form::hidden('sum_ammount',0)}}
                {{Form::hidden('exchange_ammount_bill',round($exchange_ammount_bill,2))}}
                {{Form::hidden('exchange_ammount_bill_fg',$exchange_ammount_bill_fg)}}

                {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small>
                --}}

                <div class="row">
                    <div class="col-sm-8 h-100">
                        <div class="container">
                            <h5>Datos para el registro del pago</h5>

                            <div class="dropdown-divider mb-0"></div>

                            @include('administracion.registropagos.form.exchange.ingreso')

                            <div class=" btn-group btn-block">
                                <button type="submit" class="submit btn btn-primary p-2 mt-2 w-75" value="Registrar" data-id="create" id="btn-create-registropago">
                                    <i class="far fa-save"></i>
                                    Registrar
                                </button>
                                <button type="button" class="btn-incluir btn btn-success p-2 mt-2 w-25" value="Abonar" data-id="create" id="btn-create-registropago">
                                    <i class="far fa-save"></i>
                                    Abonar
                                </button>
                            </div>



                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class=" text-right small pl-0 mt-2 mr-1">
                            @include('administracion.registropagos.form.exchange.partials.main')
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>


        </div>

    </div>

</main>

@endsection

@section('stylesheet') @parent <style type="text/css"> .form_fieldset:not(:first-of-type) { display: none; } </style> @endsection

@section('scripts')
    @parent
    <script type="text/javascript">

        //ini del evento clic
        $('.btn-incluir').click(function (e) {
            e.preventDefault();
            var form = $('#form-registropago-create-representant'); //console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data); console.log(data);
            var url = "{{ route('administracion.abonos.store') }}"; //console.log(url);
            $.post(url, data, function (result){
                // row.fadeOut(500);
                Swal.fire({
                    title: result.messenge,
                    icon: 'success'
                });
                var url = "{{ route('administracion.registropagos.create_representant_exchange',$representant->id) }}";
                window.open(url,'_self')
            }).fail(function (result) {
                Swal.fire({
                        title: 'ERROR',
                        // text: index+': '+valor,
                        type: 'error'
                    });
                var msn = '';
                $.each(result.responseJSON.errors,function(index,valor){
                    msn = msn + '<li>' + valor + '</li>'; console.log(index+valor);
                });
                msn = '<ul>'+msn+'</ul>';
                $(".alert-error").fadeOut();
                $(".alert-error").html(msn);
                $(".alert-error").fadeIn();
            });
        });
        //fin del evento clic


        $('.btn-nodal-historico').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('representant_id');  //console.log(id);
            var modal = '#modal_historico';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.representant_historico_pago", "_id_")}}';
            ajaxurl = ajaxurl.replace('_id_', id);
            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });

        $(document).ready(function() {
            if ({{($exchange_ammount_bill == 0)? 1:0}}) {
                $('#btn-create-registropago').attr('disabled','disabled');
            }
        });

        $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  //console.log(name);
                var checked = $(this).prop('checked'); console.log('name: '+name+' - checked:'+checked);
                $('#'+name).val(checked); //console.log($('#'.name).val());
            });
        });

    </script>
@endsection
