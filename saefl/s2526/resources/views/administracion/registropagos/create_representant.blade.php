@extends('administracion.layouts.dashboard.app')

@section('title') - Registro de Pagos para Representantes @endsection

@section('main')

<main role="main" id="main" class="col-md-10 ml-sm-auto col-lg-10">

    @php
        $total_a_pagar = 0
    @endphp

    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">

            <h4>
                <small class="float-right text-right small">
                    @php $exchange_ammount_bill_fg = $representant->exchange_ammount_expire_bill @endphp
                    @php $exchange_ammount_bill = ($exchange_rate_current) ? $exchange_ammount_bill_fg * $exchange_rate_current->ammount : null  @endphp
                    @php $exchange_rate_current_ammount = ($exchange_rate_current) ? $exchange_rate_current->ammount : null  @endphp

                    @if ($exchange_ammount_bill == 0)
                        <span class="badge badge-success float-right">SOLVENTE</span>
                    @else
                        <small class="small text-muted">Total deuda</small>
                        {{-- <span class="badge badge-danger">{{f_float($representant->ammount_expire_bill)}} Bs.</span> --}}
                        <span class="badge badge-danger mt-1 p-2" title="{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount) : 'Sin tasa de cambio' }}">
                            {{ ($exchange_rate_current) ? 'Bs '. f_float($exchange_ammount_bill) : 'STDC' }}
                        </span>
                        <span class="badge badge-dark">$ {{f_float($exchange_ammount_bill_fg)}}</span>
                    @endif
                    <br>
                </small>

                Registro de Pagos para Representantes

                <div class=" d-block small">
                    <small class="small text-muted">{{$representant->name}}</small>
                    @if (!empty($representant))
                        @php $registro_pago_combinados = $representant->registro_pago_combinados->sortByDesc('created_at') @endphp
                        <a title="Histórico de pagos" class="btn btn-sm btn-outline-light " data-toggle="modal" data-target="#modal_historico" href="#"role="button">
                            <i class="{{ $icon_menus['representante'] ?? ''}} fa-1x text-dark"></i>
                            <i class="{{ $icon_menus['historico'] ?? ''}} fa-1x text-primary"></i>
                        </a>
                    @endif
                </div>
            </h4>

            @include('administracion.representants.show.modal.historico') {{-- Modal --}}

        </div>

        <div class="card-body pt-2">


            @include('administracion.elements.forms.errors')

            <div class="alert-error alert-danger alert-dismissible" role="alert">
            </div>

            @include('administracion.elements.messeges.oper_ok')

            {{-- {!!Form::open(['route'=>'administracion.registropagos.store.representant','method'=>'POST','id'=>'form-registropago-create-representant','class'=>'form-signin'])!!} --}}
            {!!Form::open(['route'=>'administracion.registropagos.store.representant_exchange','method'=>'POST','id'=>'form-registropago-create-representant','class'=>'form-signin'])!!}

            {{Form::hidden('representant_id',$representant->id)}}
            {{Form::hidden('estudiant_id',$representant->estudiants->first()->id)}}
            {{Form::hidden('sum_ammount',0)}}
            {{Form::hidden('exchange_ammount_bill',round($exchange_ammount_bill,2))}}
            {{Form::hidden('exchange_ammount_bill_fg',$exchange_ammount_bill_fg)}}

            {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small>
            --}}

            <div class="row">
                <div class="col-9 h-100">
                    <div class="container">
                        <h5>Datos para el registro del pago</h5>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="dropdown-divider mb-0"></div>

                        @include('administracion.registropagos.form.fieldset.ingreso')

                        @include('administracion.registropagos.form.fieldset.cuentaxpagar')

                        @include('administracion.registropagos.form.fieldset.descuento')

                        <button type="submit" class="submit btn btn-success p-2 mt-2 btn-block" value="Registrar" data-id="create" id="btn-create-registropago">
                            <i class="far fa-save"></i>
                            Finalizar
                        </button>

                    </div>
                </div>

                <div class="col-3 text-right small pl-0">
                    {{-- <fieldset disabled="disabled"> --}}
                        {{-- @include('administracion.registropagos.partial.representant.resumen') --}}
                    {{-- </fieldset> --}}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</main>

@endsection
@section('stylesheet')
    @parent

    <style type="text/css">
        .form_fieldset:not(:first-of-type) {
            display: none;
        }
    </style>

@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
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

        //ini del evento clic
        $('.btn-incluir').click(function (e) {
            e.preventDefault();
            var form = $('#form-registropago-create-representant'); //console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data); console.log(data);
            var url = "{{ route('administracion.abonos.store') }}"; console.log(url);
            $.post(url, data, function (result){
                // row.fadeOut(500);
                Swal.fire({
                    title: result.messenge,
                    icon: 'success'
                });
                var url = "{{ route('administracion.registropagos.representant.create',$representant->id) }}";
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

        $(document).ready(function(){
            var form_count = 1, previous_form, next_form, total_forms;
            total_forms = $("fieldset").length;
            $(".next-form").click(function(){
                previous_form = $(this).parent();
                next_form = $(this).parent().next();
                next_form.fadeIn(500);
                // next_form.show();
                previous_form.fadeOut(500);
                // previous_form.hide();
                setProgressBarValue(++form_count);
            });
            $(".previous-form").click(function(){
                previous_form = $(this).parent();
                next_form = $(this).parent().prev();
                next_form.fadeIn(500);
                // next_form.show();
                previous_form.fadeOut(500);
                // previous_form.hide();
                setProgressBarValue(--form_count);
            });
            setProgressBarValue(form_count);
            function setProgressBarValue(value){
                var percent = parseFloat(100 / total_forms) * value;
                percent = percent.toFixed();
                $(".progress-bar").css("width",percent+"%").html(percent+"%");
            }

        });

        $(document).ready(function() {
            if ({{($exchange_ammount_bill == 0)? 1:0}}) {
                // $('#form-registropago-create-representant').find('input, textarea, button, select').attr('disabled','disabled');
                // $('.next-form').removeAttr('disabled','disabled').attr('enabled','enabled');
                // $('.previous-form').removeAttr('disabled','disabled').attr('enabled','enabled');
                $('#btn-create-registropago').attr('disabled','disabled');
            }
        });

        $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  //console.log(name);
                var checked = $(this).prop('checked'); console.log(checked);
                $('#'+name).val(checked); //console.log($('#'.name).val());
            });
        });

    </script>
@endsection
