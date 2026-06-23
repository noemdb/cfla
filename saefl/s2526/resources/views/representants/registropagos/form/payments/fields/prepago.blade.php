<div class="row pb-3">
    <div class="col-md-6">
        @php $name = 'method_pay_id_'.$i @endphp
        @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
        {!! Form::label($name, $list_comment_form['method_pay_id'], [
            'class' => 'font-weight-bold text-muted pb-0 mb-0',
        ]) !!}
        {!! Form::select($name, $method_pay_list, old($name), [
            'class' => 'form-control ' . $class,
            'placeholder' => 'Seleccione',
        ]) !!}
        @error($name)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-6">
        @php $name = 'banco_id_'.$i @endphp
        @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
        {!! Form::label($name, $list_comment_form['banco_id'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
        {!! Form::select($name, $banco_list, old($name), [
            'class' => 'form-control ' . $class,
            'placeholder' => 'Seleccione',
        ]) !!}
        @error($name)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row pb-3">
    <div class="col-md-6">
        @php $name = 'banco_emisor_'.$i @endphp
        @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
        @php$banco_emisor = [
                            'Banco de Venezuela' => 'Banco de Venezuela',
                            'Banco Mercantil' => 'Banco Mercantil',
                            'Banco Exterior' => 'Banco Exterior',
                            'Banco Bancaribe' => 'Banco Bancaribe',
                            'Banco del Tesoro' => 'Banco del Tesoro',
                            'Banco Provincial' => 'Banco Provincial',
                            'Banco Banesco' => 'Banco Banesco',
                            'Banco BFC' => 'Banco BFC',
                            'Banco Bicentenario' => 'Banco Bicentenario',
                            'Banco Sofitasa' => 'Banco Sofitasa',
                            'Banco Banfanb' => 'Banco Banfanb',
                            'Banco Caroní' => 'Banco Caroní',
                            'Banco Internacional' => 'Banco Internacional',
                            'Otro' => 'Otro',
                        ];
                @endphp ?>
        {!! Form::label($name, 'Banco emisor', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
        {{-- {!! Form::text($name, old($name), ['class'=>'form-control '.$class,'placeholder'=>'Banco emisor']); !!} --}}
        {!! Form::select($name, $banco_emisor, old($name), [
            'class' => 'form-control ' . $class,
            'placeholder' => 'Seleccione',
        ]) !!}
        @error($name)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-6">
        @php $name = 'number_i_pay_'.$i @endphp
        @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
        {!! Form::label($name, $list_comment_form['number_i_pay'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
        {!! Form::text($name, old($name), [
            'class' => 'form-control ' . $class,
            'placeholder' => 'Número de la transacción',
        ]) !!}
        @error($name)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row pb-3">
    <div class="col-md-6">
        @php $name = 'ammount_'.$i @endphp
        @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
        @php $help = '[Decimales separados por punto, p.ej: 10.000,00 => 10000.00]'; @endphp
        {!! Form::label($name, $list_comment_form['ammount'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
        <span class=" float-right small text-muted">{{ $help }}</span>
        {!! Form::text($name, old($name), [
            'class' => 'form-control ' . $class,
            'placeholder' => $list_comment_form['ammount'],
        ]) !!}
        @error($name)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-6">
        @php $name = 'date_transaction_'.$i @endphp
        @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
        {!! Form::label($name, $list_comment_form['date_transaction'], [
            'class' => 'font-weight-bold text-muted pb-0 mb-0',
        ]) !!}
        {!! Form::date($name, old($name), [
            'class' => 'form-control ' . $class,
            'placeholder' => 'Fecha de la transacción',
        ]) !!}
        @error($name)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row pb-3">
    <div class="col-md-6">
        @php $name = 'image_'.$i @endphp
        @php $n = $i-1 @endphp
        @php $preview_id = 'preview_'.$n @endphp

        {!! Form::label($name, 'Imagen de la tranferencia (jpg, png, pdf)', [
            'class' => 'font-weight-bold text-muted pb-0 mb-0',
        ]) !!}
        <div class="custom-file">
            {!! Form::file($name, [
                'class' => 'custom-file-input demoInputBox',
                'onchange' => "document.getElementById('" . $preview_id . "').src = window.URL.createObjectURL(this.files[0])",
                'id' => 'file',
            ]) !!}
            <label class="custom-file-label" for="inputGroupFile01">Selecciona imagen</label>
        </div>
        <img id="{{ $preview_id }}" alt="..." class="img-thumbnail rounded mx-auto d-block img-fluid mt-1"
            width="144" height="144" />
        <span id="file_error"></span>

    </div>
    {{-- <div class="col-md-6">
        @php $name = 'observation_'.$i @endphp
        @php $class = ($errors->has($name)) ? 'border border-danger rounded':null ; @endphp
        {!! Form::label($name, 'Observaciones', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
        {!! Form::textarea($name, old($name), ['class' => 'form-control'.$class,'placeholder'=>'Observaciones','rows'=>"2"]) !!}
        @error($name)<span class="text-danger small">{{ $message }}</span> @enderror
    </div> --}}
</div>

{{-- <hr> --}}
