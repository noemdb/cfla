<div class="form-group pt-2">
    <label for="registro_pago_combinado_id" class="m-0">{{ $list_comment['registro_pago_combinado_id'] }}</label>
    {!! Form::select(
        'registro_pago_combinado_id',
        $list_registro_pago_combinado,
        old('registro_pago_combinado_id'),
        [
            'wire:model.defer' => 'registro_pago_combinado_id',
            'wire:change' => 'loadCafList()',
            'class' => 'form-control',
            'id' => 'registro_pago_combinado_id',
            'placeholder' => 'Seleccione',
        ],
    ) !!}
    @error('registro_pago_combinado_id')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group pt-2">
    <label for="credito_a_favor_id" class="m-0">
        {{ $list_comment['credito_a_favor_id'] }}
    </label>
    @if ($credito_a_favor)
        <div class="badge badge-secondary  float-right px-1">
            Bs. {{ f_float($credito_a_favor->credito_ammount) }} || $ {{ f_float($credito_a_favor->exchange_ammount) }}
        </div>
    @endif
    {!! Form::select('credito_a_favor_id', $list_credito_a_favor, old('credito_a_favor_id'), [
        'wire:model.defer' => 'credito_a_favor_id',
        'wire:change' => 'loadCaf()',
        'class' => 'form-control',
        'id' => 'credito_a_favor_id',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error('credito_a_favor_id')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="observations"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['observations'] ?? '' }}</label>
    {!! Form::textarea('observations', old('observations'), [
        'wire:model.defer' => 'observations',
        'class' => 'form-control',
        'placeholder' => $list_comment['observations'],
        'id' => 'observations',
        'rows' => '4',
    ]) !!}
    @error('observations')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>
