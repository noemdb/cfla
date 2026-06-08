@if (!$estudiant->CreditosAFavorDisponibles()->isEmpty())
    <div class="form-group pt-2">
        <span class="font-weight-bold">Creditos a favor disponibles</span> 
        @foreach ($estudiant->CreditosAFavorDisponibles() as $creditoafavor)
            @component('administracion.elements.forms.check')
                @slot('name', 'credito_a_favor['.$creditoafavor->id.']')
                @slot('id', 'credito_a_favor_id_'.$creditoafavor->id)
                @slot('value', 'true')
                @slot('label', $creditoafavor->credito_description)
                @slot('name_ammount', 'credito_ammount['.$creditoafavor->id.']')
                @slot('value_ammount', $creditoafavor->credito_ammount)
                @slot('badge', 'Bs. '.f_float($creditoafavor->credito_ammount))
            @endcomponent
            <small class=" text-muted small pt-0 mt-0">
                {{$creditoafavor->credito_observations ?? ''}}
            </small>
            <div class="dropdown-divider mb-0"></div>
        @endforeach
    </div>
    {{-- <div class="form-group">
        <label for="credito_observations" class="m-0">Observaciones para el crédito a aplicar</label>
        {!! Form::text('credito_observations', old('credito_observations'), ['class' => 'form-control','placeholder'=>'Observaciones Conceptos Cancelados','id'=>'credito_observations']); !!}
    </div> --}}
@else
    <p class="text-danger font-weight-bold text-center pt-4">
        No hay créditos disponibles
    </p>
@endif

@section('scripts')
@parent

@endsection