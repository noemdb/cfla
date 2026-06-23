<div class="form-group pt-2">
    <label for="method_pay_id" class="m-0">Método de Pago</label>
    {!! Form::select('method_pay_id', $method_pay_list, old('method_pay_id'), [
        'class' => 'form-control',
        'id' => 'method_pay_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>



<div id="crt_display" class="crt_display" style="{{ old('banco_id') == 1 ? 'display:none;' : 'display:block;' }}">
    <div class="form-group">
        <label for="banco_id" class="m-0">Banco receptor </label>
        {!! Form::select('banco_id', $banco_list, old('banco_id'), [
            'class' => 'form-control',
            'id' => 'banco_id',
            'placeholder' => 'Seleccione',
            'required' => 'required',
        ]) !!}
    </div>

    <div class="form-row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="number_i_pay" class="m-0">Referencia/Número </label>
                {!! Form::text('number_i_pay', old('number_i_pay'), [
                    'class' => 'form-control crt_display',
                    'placeholder' => 'Número ',
                    'id' => 'number_i_pay',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="ingreso_ammount" class="m-0">Monto <span class="small text-muted">[Decimales separados
                        por punto]</span></label>
                {!! Form::text('ingreso_ammount', old('ingreso_ammount'), [
                    'class' => 'form-control crt_display',
                    'placeholder' => 'Monto ',
                    'id' => 'ingreso_ammount',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-6">
            <div class="form-group pb-1">
                <label for="date_payment" class="m-0">Fecha de Pago </label>
                @php $date_payment = Request::is('*edit*') && $ingreso?->date_payment ? $ingreso->date_payment->format('Y-m-d') : null; @endphp
                {!! Form::date('date_payment', $date_payment, [
                    'id' => 'date_payment',
                    'class' => 'form-control crt_display',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group pb-1">
                <label for="date_transaction" class="m-0">Fecha en Banco</label>
                @php $date_transaction = (Request::is('*edit*')) ? $ingreso->date_transaction->format('Y-m-d'):null  @endphp
                {!! Form::date('date_transaction', $date_transaction, [
                    'id' => 'date_transaction',
                    'class' => 'form-control crt_display',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="ingreso_observations" class="m-0">Observaciones para la trasacción a registrar</label>
        {!! Form::text('ingreso_observations', old('ingreso_observations'), [
            'class' => 'form-control crt_display',
            'placeholder' => 'Observaciones para la trasacción a registrar',
            'id' => 'ingreso_observations',
        ]) !!}
    </div>

    {{--
    <div class="form-group">
    <label for="person_bill_ci" class="m-0">Cédula de la Persona a quien se le registrará el pago</label>
    {!! Form::text('person_bill_ci', old('person_bill_ci'), ['class'=>'form-control','placeholder'=>'Cédula','id'=>'person_bill_ci','required'=>'required']) !!}
    </div>
    <div class="form-group">
    <label for="person_bill_name" class="m-0">Nombre de la Persona a quien se le registrará el pago</label>
    {!! Form::text('person_bill_name', old('person_bill_name'), ['class'=>'form-control','placeholder'=>'Nombre','id'=>'person_bill_name','required'=>'required']) !!}
    </div>

--}}

</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#method_pay_id').change(function(e) {
                var d = new Date();
                if ($(this).val() == 1) {
                    $("#crt_display").hide();
                    $(".crt_display").hide();
                    $("#nav-tab05-tab").tab('show');
                    $("#banco_id option[value='1']").attr("selected", true);
                    $("#number_i_pay").val(d.getTime());
                    $("#ingreso_ammount").val('0');
                    $("#date_transaction").val('2000-01-01');
                    $("#date_payment").val('2000-01-01');
                    // $("#person_bill_ci").val('0');
                    // $("#person_bill_name").val('0');
                    console.log(d.getTime());
                } else {
                    $("#crt_display").show();
                    $(".crt_display").show();
                    $("#banco_id option[value='0']").attr("selected", true);
                    $("#number_i_pay").val('');
                    $("#ingreso_ammount").val('');
                    $("#date_transaction").val('');
                    $("#date_payment").val('');
                    // $("#person_bill_ci").val('');
                    // $("#person_bill_name").val('');
                }
            });
        });

        $(document).ready(function() {
            $('#ingreso_ammount').keyup(function(e) {
                // e.preventDefault();
                var number = accounting.formatMoney($(this).val(), "", 2, ".", ",");
                $("#span_ingreso_ammount").text(number);
                console.log($(this).val());
                console.log();
            });
            //fin del evento
        });

        $(document).ready(function() {
            $('#ingreso_ammount').focusout(function(e) {});
            //fin del evento
        });
    </script>
@endsection

{{-- @section('scripts')
@parent

<script src="{{ asset("js/accounting.min.js") }}"></script>

<script src="{{ asset("vendor/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js") }}"></script>
<script src="{{asset('vendor/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.es.min.js')}}"></script>

<script type="text/javascript">
    $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            autoclose: true,
            startView: 2
        });
</script>

@endsection --}}

{{--
@section('stylesheet')
@parent

<link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">

@endsection --}}
