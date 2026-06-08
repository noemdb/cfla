{{-- {{ Form::hidden('pensum_id', $pensum->id) }} --}}
{{-- {{ Form::hidden('grado_id', $grado->id) }} --}}
{{-- {{ Form::hidden('seccion_id_old', $seccion->id) }} --}}
{{-- {{ Form::hidden('lapso_id', $lapso->id) }} --}}
{{-- {{ Form::hidden('seccion_id', $seccion->id) }} --}}

@error('unique')
    <div class="alert alert-danger font-weight-bold small py-2 my-2">{{ $message }}</div>
@enderror

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
    {{-- {!! Form::text('profesor_name', old('profesor_name'), ['wire:model'=>'profesor_name','class' => 'form-control','placeholder'=>'Nombre']); !!} --}}
    {{-- {!! Form::select('profesor_id',$list_profesors,old('profesor_id'),['wire:model'=>$model,'class'=>'form-control','placeholder'=>'Seleccione']) !!} --}}

    {{-- {{$list_profesors ?? null}} --}}

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

{{-- <div class="form-group">
    <label for="nota_type" class="font-weight-bold m-0 small">Tipo de nota final</label>
    {!! Form::select('nota_type',$tipo_list,old('nota_type'),['id'=>'nota_type','class'=>'form-control','placeholder'=>'Seleccione']) !!}
</div>

<div class="form-group">
    <label for="status_baremo" class="font-weight-bold m-0 small">Baremo</label>
    {!! Form::select('status_baremo',$baremo_apply_list,old('status_baremo'),['id'=>'status_baremo','class'=>'form-control','placeholder'=>'Seleccione']) !!}
</div>

<div class="form-group">
    <label for="status_official" class="font-weight-bold text-secondary m-0">En documentos oficiales</label>
    {!! Form::select('status_official',[true=>'SI',false=>'NO'],old('status_official'),['class'=>'form-control','id'=>'status_official','placeholder'=>'Seleccione']);!!}
</div>  

<div class="form-group">
    <label for="objetivo" class="font-weight-bold m-0 small">Objetivo</label>
    {!! Form::text('objetivo', old('objetivo'), ['class' => 'form-control','placeholder'=>'Objetivo','id'=>'objetivo']); !!}
</div>

--}}
