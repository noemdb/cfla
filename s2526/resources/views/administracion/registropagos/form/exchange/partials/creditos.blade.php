@php $creditos_disponibles_exchange = $representant->creditos_disponibles_exchange @endphp
<div class="form-group pt-2">
    <span class="font-weight-bold">Creditos a favor.</span>
    <fieldset>
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
                @slot('modalBtn', true)
                @slot('modalUrl', 'administracion.creditoafavors.modals.omit')
            @endcomponent

            @php
                $name = 'credito_exchange_ammount['.$creditoafavor->id.']';
                $value = $creditoafavor->exchange_ammount;
            @endphp
            {{ Form::hidden($name, $value) }}

            <div class="dropdown-divider mb-0"></div>
        @endforeach
    </fieldset>
</div>
