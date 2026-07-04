<div class="row">
    <div class="col">
        <div class="form-group">
            @php $readonly = (!empty($banco)) ? 'readonly':null ; @endphp
            <label for="institucion_id" class="font-weight-bold text-secondary m-0">{{$list_comment['institucion_id'] ?? ''}}</label>
            {!! Form::select('institucion_id',$institucion_list,old('institucion_id'),['class'=>'form-control','readonly','required']);!!}
        </div>
    </div>
    <div class="col">
        @component('administracion.elements.forms.input')
            @slot('name', 'name')
            @slot('value', old('name'))
            @slot('label', $list_comment['name'])
            @slot('required', 'true')
        @endcomponent
    </div>
</div>
<div class="row">
    <div class="col">
        @component('administracion.elements.forms.input')
            @slot('name', 'abbreviation')
            {{-- @slot('value', $banco->abbreviation) --}}
            @slot('value', old('abbreviation'))
            @slot('label', $list_comment['abbreviation'])
        @endcomponent
    </div>
    <div class="col">
        @component('administracion.elements.forms.input')
            @slot('name', 'description')
            @slot('value', old('description'))
            {{-- @slot('value', $banco->description) --}}
            @slot('label', $list_comment['description'])
        @endcomponent
    </div>
</div>
<div class="row">
    <div class="col">
        @component('administracion.elements.forms.input')
            @slot('name', 'number_id_bank')
            @slot('value', old('number_id_bank'))
            {{-- @slot('value',  $banco->number_id_bank) --}}
            @slot('label', $list_comment['number_id_bank'])
        @endcomponent
    </div>
    <div class="col">
        @component('administracion.elements.forms.input')
            @slot('name', 'number_acount_bank')
            @slot('value', old('number_acount_bank'))
            {{-- @slot('value',  $banco->number_acount_bank) --}}
            @slot('label', $list_comment['number_acount_bank'])
            @slot('required', 'true')
        @endcomponent
    </div>
{{-- </div>
<div class="row"> --}}
    <div class="col">
        @component('administracion.elements.forms.input')
            @slot('name', 'commission_POS_bank')
            @slot('value', old('commission_POS_bank'))
            {{-- @slot('value', $banco->commission_POS_bank) --}}
            @slot('label', $list_comment['commission_POS_bank'])
            @slot('required', 'true')
        @endcomponent
    </div>
    <div class="col">
        @component('administracion.elements.forms.input')
            @slot('name', 'commission_IGTF_bank')
            @slot('value', old('commission_IGTF_bank'))
            {{-- @slot('value',  $banco->commission_IGTF_bank) --}}
            @slot('label', $list_comment['commission_IGTF_bank'])
            @slot('required', 'true')
        @endcomponent
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="currency_id" class="font-weight-bold text-secondary m-0">{{$list_comment['currency_id'] ?? ''}}</label>
            {!! Form::select('currency_id',$list_currency,old('currency_id'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
        </div>
    </div>
    {{-- <div class="col">
        <br>
        @php $status_active_bank = ( !empty($banco->status_active_bank)) ? $banco->status_active_bank : null; @endphp
        @component('administracion.elements.forms.check')
            @slot('name', 'status_active_bank')
            @slot('value', $status_active_bank)
            @slot('label', $list_comment['status_active_bank'])
        @endcomponent
    </div> --}}
</div>

<div class="row pb-2">
    <div class="col">
        @php $status_active_bank = ( !empty($banco->status_active_bank)) ? $banco->status_active_bank : null; @endphp
        @component('administracion.elements.forms.check')
            @slot('name', 'status_active_bank')
            @slot('value', $status_active_bank)
            @slot('label', $list_comment['status_active_bank'])
        @endcomponent
    </div>
    <div class="col">
        @php $is_public = ( !empty($banco->is_public)) ? $banco->is_public : null; @endphp
        @component('administracion.elements.forms.check')
            @slot('name', 'is_public')
            @slot('value',  $is_public)
            @slot('label', $list_comment['is_public'])
        @endcomponent
    </div>
    <div class="col">
        @php $is_intern = ( !empty($banco->is_intern)) ? $banco->is_intern : null; @endphp
        @component('administracion.elements.forms.check')
            @slot('name', 'is_intern')
            @slot('value',  $is_intern)
            @slot('label', $list_comment['is_intern'])
        @endcomponent
    </div>
    <div class="col">
        @php $is_cash = ( !empty($banco->is_cash)) ? $banco->is_cash : null; @endphp
        @component('administracion.elements.forms.check')
            @slot('name', 'is_cash')
            @slot('value',  $is_cash)
            @slot('label', $list_comment['is_cash'])
        @endcomponent
    </div>
</div>

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                //alert('123');
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  console.log(name);
                var checked = $(this).prop('checked'); console.log(checked);
                var input = '[name='+name+']';
                $(input).val(checked); console.log($(input).val());
            });
        });
    </script>
@endsection
