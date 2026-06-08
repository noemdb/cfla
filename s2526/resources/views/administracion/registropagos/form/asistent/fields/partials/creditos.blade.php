@php
$creditos_disponibles_exchange = $representant->creditos_disponibles_exchange;
$total_exchange_ammount = $creditos_disponibles_exchange->sum('exchange_ammount');
$modalBtn = (Auth::user()->isAdmon()) ? true :false ;
$modalUrl = (Auth::user()->isAdmon()) ? 'administracion.creditoafavors.modals.omit' :null ;
@endphp
<div class="form-group pt-2"  >
    <span class="font-weight-bold">Creditos a Favor</span>
    @if ($total_exchange_ammount)
        {{-- <fieldset id="crt_display_caf"> --}}
            @foreach ($creditos_disponibles_exchange as $creditoafavor)
                @component('administracion.elements.forms.check')
                    @slot('name', 'credito['.$creditoafavor->id.']')
                    @slot('id', $creditoafavor->id)
                    @slot('value', 'true')
                    @slot('label', '')
                    @slot('readonly', 'readonly')
                    @slot('name_ammount', 'credito_ammount['.$creditoafavor->id.']')
                    @slot('value_ammount', $creditoafavor->exchange_ammount)
                    @slot('badge', 'Bs. '.f_float($creditoafavor->credito_ammount).' | $ '.f_float($creditoafavor->exchange_ammount))
                    @slot('modalBtn', $modalBtn)
                    @slot('modalUrl', $modalUrl)
                @endcomponent

                @php
                    $name = 'credito_ammount_base['.$creditoafavor->id.']';
                    $value = $creditoafavor->credito_ammount;
                @endphp
                {{ Form::hidden($name, $value) }}

                @php
                    $name = 'credito_exchange_ammount['.$creditoafavor->id.']';
                    $value = $creditoafavor->exchange_ammount;
                @endphp
                {{ Form::hidden($name, $value) }}

                <div class="dropdown-divider mb-0"></div>
            @endforeach
        {{-- </fieldset> --}}
    @else
    <span class="small text-muted">NO HAY CREDITOS A FAVOR</span>
    @endif

</div>
