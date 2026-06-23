@extends('administracion.layouts.dashboard.app')

@section('main')

<main role="main" id="main" class="col-md-10 ml-sm-auto col-lg-10">
    @php
    $total_pagado = $cuentaxpagar->TotalMontoConceptosPagados($estudiant->id);
    $monto_descuento = $estudiant->descuento_ammount($cuentaxpagar->id);
    $descuento = 1 - ($monto_descuento / 100);
    $total_concepto = ($monto_descuento>0) ? ( $cuentaxpagar->SumaConceptos() * (1 - ($monto_descuento / 100))): $cuentaxpagar->SumaConceptos();
    $total_concepto_descuento = $cuentaxpagar->SumaConceptosDescuentos($estudiant->id);
    // $total_concepto_descuento = ($estudiant->descuento > 0) ? ( $cuentaxpagar->SumaConceptos() * (1 - ($estudiant->descuento / 100))): $cuentaxpagar->SumaConceptos();
    $total_a_pagar = $cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id);
    // $total_a_pagar = round($total_concepto_descuento - $total_pagado,2);
    @endphp
    {{-- total_pagado: {{$total_pagado  ?? 'fallo'}}<br>
    monto_descuento: {{$monto_descuento  ?? 'fallo'}}<br>
    descuento: {{$descuento  ?? 'fallo'}}<br>
    total_concepto: {{$total_concepto  ?? 'fallo'}}<br>
    total_concepto_descuento: {{$total_concepto_descuento  ?? 'fallo'}}<br>
    total_a_pagar: {{$total_a_pagar  ?? 'fallo'}}<br> --}}

    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <h3>
                Datos para el registro del pago<br>
                <small class="text-default">
                    {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                </small>

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    {{-- @include('administracion.configuraciones.menus.index') --}}

                </div>
                {{-- FIN Menu rapido --}}

            </h3>
        </div>

        <div class="card-body pt-2">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!!Form::open(['route'=>'administracion.registropagos.store','method'=>'POST','id'=>'form-registropago-create','class'=>'form-signin'])!!}

            {{Form::hidden('estudiant_id',$estudiant->id)}}

            <div class="row">
                <div class="col-2 p-0">
                    @include('administracion.registropagos.deck.card.estudiant_simple')
                </div>
                <div class="col-7 h-100">
                    <div class="bd-callout bd-callout-{{($cuentaxpagar->StateExpireBill($estudiant->id)) ? 'danger':'success bg-light'}}">
                        <div class="card p-0 {{($cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id)>0) ? '':'bg-light'}}">
                            <div class="card-header pb-0 mb-0">
                                <div class="form-group pt-2">
                                    <label for="cuentaxpagar_id" class="m-0">Conceptos por cobrar</label>
                                    {!!Form::select('cuentaxpagar_id',$cuentaxpagar_list,$cuentaxpagar->id,['class'=>'form-control','id'=>'cuentaxpagar_id','placeholder' => 'Seleccione','required'=>'required']);!!}
                                </div>
                            </div>
                            <div class="card-body p-3">

                                @include('administracion.registropagos.partial.navtabs')

                                <div class="align-bottom">
                                    <button type="submit" class="btn-registropago-create btn btn-primary btn-block mt-1"
                                        value="Registrar" data-id="create"
                                        id="btn-create-registropago-{{$estudiant->id ?? ''}}">
                                        <i class="far fa-save"></i>
                                        Registrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3 text-right small pl-0">
                    @include('administracion.registropagos.partial.resumen')
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</main>

@endsection

@section('scripts')
@parent
<script type="text/javascript">
        $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  console.log(name);
                var checked = $(this).prop('checked'); console.log(checked);
                $('#'+name).val(checked); console.log($('#'.name).val());
            });
        });

        $(document).ready(function() {
            if ({{($total_a_pagar == 0)? 1:0}}) {
                $('#form-registropago-create').find('input, textarea, button, select').attr('disabled','disabled');
                $('#cuentaxpagar_id').removeAttr('disabled','disabled').attr('enabled','enabled');
            }
        });

        $(document).ready(function(){
            $("#cuentaxpagar_id").change(function(){
                var cuentaxpagar_id = $(this).val();console.log(cuentaxpagar_id);
                var url = "{{ route('administracion.registropagos.create',[$estudiant->id,'']) }}/"+cuentaxpagar_id;
                window.open(url,'_self')
            });
        });
</script>
@endsection
