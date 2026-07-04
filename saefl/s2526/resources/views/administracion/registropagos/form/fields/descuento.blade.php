@if (count($estudiant->planbeneficos) > 0)
    <div class="form-group pt-2">
        @foreach ($estudiant->planbeneficos as $planbenefico)
            @php $descuento = $planbenefico->descuento @endphp
            @component('administracion.elements.forms.check')
                @slot('name', 'descuento[' . $descuento->id . ']')
                @slot('id', 'descuento_id' . $descuento->id)
                @slot('value', 'true')
                @slot('disabled', 'disabled')
                @slot('label', $descuento->descuento_name)
                @slot('badge', $descuento->descuento_ammount . '%'))
            @endcomponent
        @endforeach
    </div>
    <div class="form-group">
        <label for="descuento_observations" class="m-0">Observaciones descuentos aplicados</label>
        {!! Form::text('descuento_observations', old('descuento_observations'), [
            'class' => 'form-control',
            'placeholder' => 'Observaciones conceptos cancelados',
            'id' => 'ammount',
        ]) !!}
    </div>
@else
    <p class="text-danger font-weight-bold text-center pt-4">
        No hay Planes Benéficos
    </p>
@endif
