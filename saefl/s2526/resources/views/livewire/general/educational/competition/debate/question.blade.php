<div class="p-2 m-2">

    <div class="alert alert-secondary mb-4 fw-bold">Registrar nueva pregunta:</div>

    <div class="form-group py-2">
        @php
            $name = 'category';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $model }}" class=" fw-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::select($model, $list_category, old($model), [
            'wire:model' => $model,
            'class' => 'form-select',
            'id' => $model,
            'placeholder' => 'Selecciones',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group py-2">
        @php
            $name = 'text';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $name ?? null }}" class=" fw-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment[$name],
            'rows' => '2',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group py-2">
        @php
            $name = 'observation';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $name ?? null }}" class=" fw-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment[$name],
            'rows' => '2',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group py-2">
        @php
            $name = 'time';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $name ?? null }}" class=" fw-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::number($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment[$name],
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group py-2">
        @php
            $name = 'weighting';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $name ?? null }}" class=" fw-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::number($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment[$name],
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    {!! Form::button('Guardar', [
        'class' => 'form-control btn pt-1 mt-1 btn-primary w-100',
        'wire:click' => 'save()',
        'wire:loading.class' => 'disabled',
    ]) !!}

</div>
