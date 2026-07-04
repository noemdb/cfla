@extends('administracion.layouts.dashboard.app')

@section('title') - Asistente Registro de Pagos - Representantes @endsection

@section('main')

<main role="main" id="main" class="col-md-10 ml-sm-auto col-lg-10">
    @php $total_a_pagar = 0; @endphp
    @php $messenge_refund = (Session::has('messenge_refund')) ? Session::get('messenge_refund'): null @endphp
    @php $pago_combinado_id = (Session::has('pago_combinado_id')) ? Session::get('pago_combinado_id'): null @endphp

    @if (Session::has('operp_ok')) @includeWhen($pago_combinado_id,'administracion.registropagos.form.asistent.sweetalerts.questions') @endif

    @if (Session::has('messenge_refund')) @include('administracion.registropagos.form.asistent.sweetalerts.refund') @endif

    @if (Session::has('operpNoOk')) @include('administracion.registropagos.form.asistent.sweetalerts.error') @endif

    <div class="card card-primary mt-2">
        {{-- {{$inputs ?? null}} --}}
        <div class="card-header pb-0 mb-0 alert-secondary">

            <h4>
                <small class="float-right text-right small">

                    @php
                        $exchange_ammount_bill_fg = round($representant->exchange_ammount_expire_bill, 2);
                        $exchange_ammount_bill = ($exchange_rate_current) ? $exchange_ammount_bill_fg * $exchange_rate_current->ammount : null;
                        $exchange_rate_current_ammount = ($exchange_rate_current) ? $exchange_rate_current->ammount : null;
                        $exchange_ammount_unexpired_bill = $representant->exchange_ammount_unexpired_bill;

                        $exchange_ammount_bill = round($exchange_ammount_bill,2);
                        $exchange_ammount_bill_fg = round($exchange_ammount_bill_fg,2);

                        $status_disabled = ($exchange_ammount_bill == 0 && $exchange_ammount_unexpired_bill == 0)? true:false;
                    @endphp

                    @if ($exchange_ammount_bill_fg == 0)
                        <span class="badge badge-success float-right">SOLVENTE</span>
                    @else
                        <small class="small text-muted">Total deuda</small>
                        <span class="badge badge-danger mt-1 p-2" title="{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount) : 'Sin tasa de cambio' }}">
                            {{ ($exchange_rate_current) ? 'Bs '. f_float($exchange_ammount_bill) : 'STDC' }}
                        </span>
                        <span class="badge badge-dark">$ {{f_float($exchange_ammount_bill_fg)}}</span>
                    @endif
                    <br>
                </small>

                Asistente para el Registro de Pagos - Representantes

                @if($messenge_refund)
                <div class="alert alert-success">
                    {!!$messenge_refund ?? 'fallo'!!}
                </div>
                @endif

                <div class=" d-block small">
                    @if (!empty($representant))
                    <small class="small text-muted">{{$representant->name}}</small>
                    <small class="small text-muted">{{$representant->ci_representant}}</small>
                    @endif
                </div>
            </h4>

        </div>

        <div class="card-body pt-2">

            {{-- @include('administracion.elements.messeges.oper_ok') --}}

            {!!Form::open(['route'=>'administracion.registropagos.asistent.store.representant','method'=>'POST','id'=>'form-registropago-asistent','class'=>'form-signin'])!!}

                {{Form::hidden('representant_id',$representant->id)}}
                {{Form::hidden('estudiant_id',$representant->estudiants->first()->id)}}
                {{Form::hidden('sum_ammount',0)}}
                {{Form::hidden('exchange_ammount_bill',round($exchange_ammount_bill,2))}}
                {{Form::hidden('exchange_ammount_bill_fg',$exchange_ammount_bill_fg)}}

                <div class="row">
                    <div class="col-9 h-100">
                        <div class="container-fluid shadow">

                            <h5>Datos para el registro del pago</h5>

                            <div class="px-2">

                                <div class="progress"> <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div></div>

                                <hr>

                                <fieldset {{ ($status_disabled) ? 'disabled':null }}>
                                    @include('administracion.registropagos.form.asistent.fieldset.ingreso')
                                    @include('administracion.registropagos.form.asistent.fieldset.recursos')
                                    @include('administracion.registropagos.form.asistent.fieldset.cuentaxpagars')
                                    @include('administracion.registropagos.form.asistent.fieldset.adelantados')
                                    @include('administracion.registropagos.form.asistent.fieldset.finalizar')
                                </fieldset>

                            </div>

                        </div>
                    </div>

                    <div class="col-3 pl-0">

                        <div class="table-secondary px-2 border rounded">

                            @include('administracion.elements.forms.errors')

                            <div class="text-right"> @include('administracion.registropagos.form.asistent.partials.representant.resumen') </div>

                        </div>

                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</main>

@endsection

@section('stylesheet') @parent <style type="text/css"> .first-of-type { display: none; } </style> @endsection

@section('scripts')
    @parent

    <script type="text/javascript">

        $(document).ready(function(){

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            var form_count = 1, previous_form, next_form, total_forms;
            total_forms = $("fieldset .form_fieldset").length;

            $(".next-form").click(function(){
                prev_frm_name = $(this).data('frm-prev');
                prev_frm = $('#'+prev_frm_name);
                next_frm_name = $(this).data('frm-next');
                next_frm = $('#'+next_frm_name);
                prev_frm.hide();
                next_frm.fadeIn(800).delay( 100 );
                direction = $(this).data('direction');
                if (direction=="up") { setProgressBarValue(++form_count); }
                if (direction=="down") { setProgressBarValue(--form_count);}

                // Toast.fire({
                //     icon: 'success',
                //     title: 'Paso: '+form_count
                // });

            });

            setProgressBarValue(form_count);
            function setProgressBarValue(value){
                var percent = parseFloat(100 / total_forms) * value;
                percent = percent.toFixed();
                $(".progress-bar").css("width",percent+"%").html(percent+"%");
            }
        });

        $(document).ready(function() {
            if ( {{ ($status_disabled) ? 1:0 }} ) {
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
