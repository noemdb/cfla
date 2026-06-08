{{-- {{ $mbancarioXLS['error_mbancario_number_i_pay'] ?? 'FALLO'}} --}}

@if ($mbancarioXLS['error_mbancario_number_i_pay'])
    @php
        $data = collect();
        $data->put('field',$mbancarioXLS['number_i_pay']);
        $data->put('error','NÚMERO DE REFERENCIA YA REGISTRADO');
        $data->put('class',$mbancarioXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($mbancarioXLS['error_number_i_pay'])
    @php
        $data = collect();
        $data->put('field',$mbancarioXLS['number_i_pay']);
        $data->put('error','NÚMERO DE REFERENCIA YA USADO');
        $data->put('class',$mbancarioXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($mbancarioXLS['error_ingreso_ammount'])
    @php
        $data = collect();
        $data->put('field',$mbancarioXLS['ingreso_ammount']);
        $data->put('error','MONTO INCORRECTO (FORMATO: numérico)');
        $data->put('class',$mbancarioXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($mbancarioXLS['error_date_transaction_xls'])
    @php
        $data = collect();
        $data->put('field',$mbancarioXLS['date_transaction']);
        $data->put('error','FECHA INCORRECTA (FORMATO: dd/mm/aaaa)');
        $data->put('class',$mbancarioXLS['class']);
        $row_error->push($data);
    @endphp
@endif

@if ($row_error->count() > 0)

    @php $errors->put($loop->iteration,$row_error); @endphp

@endif

