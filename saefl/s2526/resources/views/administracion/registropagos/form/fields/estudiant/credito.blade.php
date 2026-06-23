<div class="form-group pt-2 mb-1 pb-1">
    @foreach ($estudiant->creditos_disponibles as $creditoafavor)
        @component('administracion.elements.forms.check')
            @slot('name', 'credito_a_favor['.$creditoafavor->id.']')
            @slot('id', 'credito_a_favor_id_'.$creditoafavor->id)
            @slot('value', 'true')
            @slot('label', '')
            @slot('name_ammount', 'credito_ammount['.$creditoafavor->id.']')
            @slot('value_ammount', $creditoafavor->credito_ammount)
            @slot('badge', $loop->iteration.'. CAF: Bs '.f_float($creditoafavor->credito_ammount))
        @endcomponent
        {{-- <small class=" text-muted small pt-0 mt-0">
            {{$creditoafavor->credito_observations ?? ''}}
        </small> --}}
        <div class="dropdown-divider mb-0"></div>
    @endforeach
</div>
