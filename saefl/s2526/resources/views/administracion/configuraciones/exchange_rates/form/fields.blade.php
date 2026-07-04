{{ Form::hidden('user_id', Auth::user()->id) }}

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="currency_id" class="font-weight-bold text-secondary m-0">{{$list_comment['currency_id'] ?? ''}}</label>
            {!! Form::select('currency_id',$list_currency,old('currency_id'),['class'=>'form-control','required']);!!}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="currency_referential_id" class="font-weight-bold text-secondary m-0">{{$list_comment['currency_referential_id'] ?? ''}}</label>
            {!! Form::select('currency_referential_id',$list_referential_currency,old('currency_referential_id'),['class'=>'form-control','required']);!!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        @component('administracion.elements.forms.input')
            @slot('name', 'ammount')
            @slot('value', old('ammount'))
            @slot('label', $list_comment['ammount'])
            @slot('required', 'true')
        @endcomponent
    </div>
    <div class="col-sm-6">
        @component('administracion.elements.forms.input')
            @slot('name', 'ammount_buy')
            @slot('value', old('ammount_buy'))
            @slot('label', $list_comment['ammount_buy'])
            {{-- @slot('required', 'false') --}}
        @endcomponent
    </div>

</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group pb-1">
            <label for="date" class="m-0 font-weight-bold text-muted">{{$list_comment['date'] ?? ''}}</label>
            @php $date = (!empty($exchange_rate)) ? $exchange_rate->date : null ; @endphp
            {!! Form::date('date', $date,['class'=>'form-control','placeholder'=>$list_comment['date'],'required'=>'required']) !!}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group pb-1">
            {{-- @component('administracion.elements.forms.input')
            @slot('name', 'source')
            @slot('value', old('source'))
            @slot('label', $list_comment['source'])
            @slot('required', 'false')
            @endcomponent --}}
            <div class="form-group">
                <label for="source" class="font-weight-bold text-secondary m-0">{{$list_comment['source'] ?? ''}}</label>
                {!! Form::select('source',['BCV'=>'BCV','Otros'=>'Otros'],old('source'),['class'=>'form-control','required']);!!}
            </div>
        </div>
    </div>
</div>

@component('administracion.elements.forms.input')
    @slot('name', 'observations')
    @slot('value', old('observations'))
    @slot('label', $list_comment['observations'])
    {{-- @slot('required', 'false') --}}
@endcomponent

{{-- @php $status_official = ( !empty($planpago->status_official)) ? $planpago->status_official:null; @endphp
@php $disabled      = ( !empty($planpago->status_official) && !empty($planpago->id)) ? null : ' disabled ' ; @endphp
@component('administracion.elements.forms.check')
    @slot('name', 'status_official')
    @slot('value',  $status_official)
    @slot('label', $list_comment['status_official'])
@endcomponent --}}

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  console.log(name);
                var checked = $(this).prop('checked'); console.log(checked);
                var input = '[name='+name+']';
                $(input).val(checked); console.log($(input).val());
            });
        });
    </script>
@endsection
