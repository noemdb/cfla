@if ($ingresoXLS['error_mbancario_number_i_pay'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['number_i_pay']);
        $data->put('error','NO SE ENCONTRÓ EL NÚMERO DE REFERENCIA');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@else



@if ($ingresoXLS['error_representant'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['ci_representant_xls']);
        $data->put('error','CÉDULA NO ENCONTRADA');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($ingresoXLS['error_method_pay'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['method_pay_name_xls']);
        $data->put('error','MÉTODO DE PAGO NO ENCONTRADO');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($ingresoXLS['error_banco'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['banco_name_xls']);
        $data->put('error','BANCO NO ENCONTRADO');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($ingresoXLS['error_prepago_number_i_pay'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['number_i_pay']);
        $data->put('error','NÚMERO DE REFERENCIA YA NOTIFICADO');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($ingresoXLS['error_number_i_pay'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['number_i_pay']);
        $data->put('error','NÚMERO DE REFERENCIA YA USADO');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($ingresoXLS['error_ingreso_ammount'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['ingreso_ammount']);
        $data->put('error','MONTO INCORRECTO (FORMATO: numérico)');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($ingresoXLS['error_date_transaction_xls'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['date_transaction']);
        $data->put('error','FECHA INCORRECTA (FORMATO: dd/mm/aaaa)');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($ingresoXLS['error_date_transaction_bco'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['date_transaction'].' - '.$ingresoXLS['date_transaction_bco']);
        $data->put('error','INCONSISTENCIAS EN LAS FECHAS');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($ingresoXLS['error_ingreso_ammount_bco'])
    @php
        $data = collect();
        $data->put('field',$ingresoXLS['ingreso_ammount'].' - '.$ingresoXLS['ingreso_ammount_bco']);
        $data->put('error','INCONSISTENCIAS EN LOS MONTOS');
        $data->put('class',$ingresoXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@endif

@if ($row_error->count() > 0)

    @php $errors->put($loop->iteration,$row_error); @endphp

@endif

