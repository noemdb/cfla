@component('administracion.elements.forms.input')
    @slot('name', 'name')
    @slot('value', old('name'))
    @slot('label', $list_comment['name'])
    @slot('required', 'true')
@endcomponent

<div class="form-group">
    <label for="currency_id" class="font-weight-bold text-secondary m-0">{{$list_comment['currency_id'] ?? ''}}</label>
    {!! Form::select('currency_id',$list_currency,old('currency_id'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
</div>
<div class="form-group">
    <label for="referential_currencie_id" class="font-weight-bold text-secondary m-0">{{$list_comment['referential_currencie_id'] ?? ''}}</label>
    {!! Form::select('referential_currencie_id',$list_referential_currency,old('referential_currencie_id'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
</div>

@component('administracion.elements.forms.input')
    @slot('name', 'description')
    @slot('value', old('description'))
    @slot('label', $list_comment['description'])
    @slot('required', 'true')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'observations')
    @slot('value',  old('observations'))
    @slot('label', $list_comment['observations'])
    @slot('required', 'true')
@endcomponent

@php $status_active = ( !empty($planpago->status_active)) ? $planpago->status_active:null; @endphp
@php $disabled      = ( !empty($planpago->status_delete) && !empty($planpago->id)) ? null : ' disabled ' ; @endphp
@component('administracion.elements.forms.check')
    @slot('name', 'status_active')
    @slot('value',  $status_active)
    @slot('label', $list_comment['status_active'])
    {{-- @slot('disabled', $disabled) --}}
@endcomponent
@php $status_foreign_currency = ( !empty($planpago->status_foreign_currency)) ? $planpago->status_foreign_currency:null; @endphp
@php $disabled      = ( !empty($planpago->status_foreign_currency) && !empty($planpago->id)) ? null : ' disabled ' ; @endphp
@component('administracion.elements.forms.check')
    @slot('name', 'status_foreign_currency')
    @slot('value',  $status_foreign_currency)
    @slot('label', $list_comment['status_foreign_currency'])
    {{-- @slot('disabled', $disabled) --}}
@endcomponent

@php $status_inscription_affects = ( !empty($planpago->status_inscription_affects)) ? $planpago->status_inscription_affects:null; @endphp
@php $disabled      = ( !empty($planpago->status_inscription_affects) && !empty($planpago->id)) ? null : ' disabled ' ; @endphp
@component('administracion.elements.forms.check')
    @slot('name', 'status_inscription_affects')
    @slot('value',  $status_inscription_affects)
    @slot('label', $list_comment['status_inscription_affects'])
    {{-- @slot('disabled', $disabled) --}}
@endcomponent

@php $status_cancel = ( !empty($planpago->status_cancel)) ? $planpago->status_cancel:null; @endphp
@component('administracion.elements.forms.check')
    @slot('name', 'status_cancel')
    @slot('value',  $status_cancel)
    @slot('label', $list_comment['status_cancel'])
    {{-- @slot('disabled', $disabled) --}}
@endcomponent

@php $enabled_for_administrative = ( !empty($planpago->enabled_for_administrative)) ? $planpago->enabled_for_administrative:null; @endphp
@component('administracion.elements.forms.check')
    @slot('name', 'enabled_for_administrative')
    @slot('value',  $enabled_for_administrative)
    @slot('label', $list_comment['enabled_for_administrative'])
    {{-- @slot('disabled', $disabled) --}}
@endcomponent


{{-- <label for="status_foreign_currency" class="font-weight-bold text-secondary m-0">{{$list_comment['status_foreign_currency'] ?? ''}}</label>
<div class="form-group">
    {!! Form::select('status_foreign_currency',[true=>'SI',false=>'NO'],old('status_foreign_currency'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
</div> --}}

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

        $(document).ready(function() {
            if ( {{(Auth::user()->isAdmin()) ? 0:1}} ) {
                $('#form-update-institucion').find('input, textarea, button, select').attr('disabled','disabled');
            }
        });
    </script>
@endsection
