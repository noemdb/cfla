@error('unique')
    <div class="alert alert-danger font-weight-bold small py-2 my-2">{{ $message }}</div>
@enderror

<div class="form-group">
    @php
        $name = 'pestudio_id';
        $model = '' . $name;
    @endphp
    <label for="{{ $model }}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::select($name, $list_pestudio, old($name), [
        'wire:model' => $model,
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    @php
        $name = 'grado_id';
        $model = '' . $name;
    @endphp
    <label for="{{ $model }}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::select('grado_id', $list_grado, old('grado_id'), [
        'wire:model' => $model,
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    @php
        $name = 'seccion_id';
        $model = '' . $name;
    @endphp
    <label for="seccion_id" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::select('seccion_id', $list_seccion, old('seccion_id'), [
        'wire:model' => $model,
        'class' => 'form-control',
        'id' => 'seccion_id',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    @php
        $name = 'lapso_id';
        $model = '' . $name;
    @endphp
    <label for="lapso_id" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::select('lapso_id', $list_lapso, old('lapso_id'), [
        'wire:model' => $model,
        'class' => 'form-control',
        'id' => 'seccion_id',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    @php
        $name = 'pensum_id';
        $model = '' . $name;
    @endphp
    <label for="pensum_id" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::select('pensum_id', $list_pensum, old('pensum_id'), [
        'wire:model' => $model,
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    @php
        $name = 'profesor_id';
        $model = '' . $name;
    @endphp
    <label for="profesor_id" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>

    <div class="input-group mb-3">
        <div class="input-group-prepend">
            {!! Form::text('profesor_name', old('profesor_name'), [
                'wire:model' => 'profesor_name',
                'class' => 'form-control',
                'placeholder' => 'Buscar por nombre',
            ]) !!}
        </div>
        {!! Form::select('profesor_id', $list_profesors, old('profesor_id'), [
            'wire:model' => $model,
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
        ]) !!}
        <div class="input-group-prepend">
            <div class="font-weight-bold small text-muted" wire:loading wire:target="profesor_name">Procesando...</div>
        </div>
    </div>
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    @php
        $name = 'description';
        $model = '' . $name;
    @endphp
    <label for="description" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::text('description', old('description'), [
        'wire:model' => $model,
        'class' => 'form-control',
        'placeholder' => 'Descripción',
        'id' => 'Descripción',
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    @php
        $name = 'grupo_estable_id';
        $model = '' . $name;
    @endphp
    <label for="grupo_estable_id" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::select('grupo_estable_id', $list_grupo_estable, old('grupo_estable_id'), [
        'wire:model' => $model,
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    @php
        $name = 'status_note_report';
        $model = '' . $name;
    @endphp
    <label for="status_note_report" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::select('status_note_report', [true => 'SI', false => 'NO'], old('status_note_report'), [
        'wire:model' => $model,
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>
