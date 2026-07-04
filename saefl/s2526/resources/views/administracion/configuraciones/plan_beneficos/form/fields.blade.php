<label for="descuento_id" class="m-0 font-weight-bold text-muted">Descuento</label>
<div class="input-group mb-3">
    {!! Form::select('descuento_id', $list_descuentos, old('descuento_id'), ['class' => 'form-control']) !!}
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group pb-1">
            <label for="created_at" class="m-0 font-weight-bold text-muted">Fecha Inicial</label>
            {{-- @php $created_at = (is_countable($plan_benefico)) ? $plan_benefico->created_at : null ; @endphp --}}
            @php $created_at = (!empty($plan_benefico)) ? $plan_benefico->created_at : null ; @endphp
            {!! Form::date('created_at', $created_at, [
                'class' => 'form-control',
                'placeholder' => 'Fecha Inicial',
                'required' => 'required',
            ]) !!}
            {{-- {!! Form::text('created_at', old('created_at'), ['class'=>'form-control datepicker','placeholder'=>'Fecha Inicial','id'=>'created_at','required'=>'required','readonly','maxlength'=>"10"]); !!} --}}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group pb-1">
            <label for="ffinal" class="m-0 font-weight-bold text-muted">Fecha Final</label>
            @php $ffinal = (!empty($plan_benefico)) ? $plan_benefico->ffinal : null ; @endphp
            {!! Form::date('ffinal', $ffinal, [
                'class' => 'form-control',
                'placeholder' => 'Fecha Final',
                'required' => 'required',
            ]) !!}
            {{-- {!! Form::text('ffinal', old('ffinal'), ['class'=>'form-control datepicker','placeholder'=>'Fecha Final','id'=>'ffinal','required'=>'required','readonly','maxlength'=>"10"]); !!} --}}
        </div>
    </div>
</div>

<div class="form-group">
    <label for="observations" class="m-0 font-weight-bold text-muted">Observaciones</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observaciones',
        'id' => 'observations',
    ]) !!}
</div>

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
@endsection

@section('stylesheet')
    @parent
    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endsection --}}
