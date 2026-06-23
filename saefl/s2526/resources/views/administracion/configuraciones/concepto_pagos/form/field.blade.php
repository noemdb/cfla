<label for="type_id" class="font-weight-bold text-secondary m-0">Tipo</label>
<div class="input-group mb-3">
    {!! Form::select('type_id', ['GENERAL' => 'GENERAL', 'INDIVIDUAL' => 'INDIVIDUAL'], old('type_id'), [
        'required',
        'class' => 'form-control',
        'id' => 'type_id',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<label for="cuentaxpagar_id" class="m-0 font-weight-bold text-secondary">Concepto de cobro [Plan de Pago ||
    Concepto]</label>
<div class="input-group mb-3">
    {!! Form::select('cuentaxpagar_id', $list_cuentaxpagar, old('cuentaxpagar_id'), [
        'class' => 'form-control',
        'id' => 'cuentaxpagar_id',
    ]) !!}
</div>

<label for="nom_concepto_pago_id"
    class="m-0 font-weight-bold text-secondary">{{ $list_comment['nom_concepto_pago_id'] ?? '' }}</label>
<div class="input-group mb-3">
    {!! Form::select('nom_concepto_pago_id', $list_nom_concepto_pago, old('nom_concepto_pago_id'), [
        'class' => 'form-control',
    ]) !!}
</div>

<div class="form-group pb-3">
    <label for="concepto_ammount"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['concepto_ammount'] ?? '' }}</label>
    {{-- {!! Form::hidden('concepto_ammount', 1) !!} --}}
    {!! Form::text('concepto_ammount', old('concepto_ammount'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['concepto_ammount'],
        'id' => 'concepto_ammount',
        'required',
    ]) !!}
</div>

<div class="form-group pb-3">
    <label for="exchange_ammount"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['exchange_ammount'] ?? '' }}</label>
    {!! Form::text('exchange_ammount', old('exchange_ammount'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['exchange_ammount'],
        'id' => 'exchange_ammount',
        'required',
    ]) !!}
</div>

<div class="form-group pb-3">
    <label for="concepto_description"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['concepto_description'] ?? '' }}</label>
    {!! Form::text('concepto_description', old('concepto_description'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['concepto_description'],
        'id' => 'concepto_description',
    ]) !!}
</div>

<div class="form-group pb-3">
    <label for="concepto_observations"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['concepto_observations'] ?? '' }}</label>
    {!! Form::text('concepto_observations', old('concepto_observations'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['concepto_observations'],
        'id' => 'concepto_observations',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="status_discount"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['status_discount'] ?? '' }}</label>
    {!! Form::select('status_discount', ['true' => 'SI', 'false' => 'NO'], old('status_discount'), [
        'class' => 'form-control',
        'id' => 'status_discount',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $("#type_id").change(function() {
                var type_id = $(this).val();
                console.log(type_id);
                console.log('cuentaByconcepto/' + type_id);
                $.ajax({
                        type: "GET",
                        url: "{{ route('administracion.ajax.fill.cuentaByconcepto', '') }}/" + type_id,
                        data: {
                            type_id: type_id
                        }
                    })
                    .done(function(data) {
                        console.log(data);
                        var seccion_select = '<option value="">Seleccione</option>'
                        for (var i = 0; i < data.length; i++)
                            seccion_select += '<option value="' + data[i].id + '">' + data[i].name +
                            '</option>';
                        $("#cuentaxpagar_id").html(seccion_select);
                    })
                    .fail(function() {
                        console.log("error occured");
                    });

            });
        });
    </script>
@endsection
