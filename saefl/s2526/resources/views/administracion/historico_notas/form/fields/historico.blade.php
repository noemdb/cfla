<div class="form-group py-2">
    <label for="pestudio_id" class="m-0 font-weight-bold text-secondary">Plan de Estudio: </label>
    {{-- {!! Form::select('pestudio_id',$list_pestudio,old('pestudio_id'),['id'=>'pestudio_id','class'=>'form-control','placeholder'=>'Seleccione','required']) !!} --}}
    <div>
        {{-- <strong> --}}
        {{ ucwords(strtolower($pestudio->name)) ?? '' }}
        {{-- </strong> --}}
    </div>
</div>

<div class="form-group">
    <label for="observations" class="m-0 font-weight-bold text-secondary">Observación</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observación',
        'id' => 'observations',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="fecha_expedicion" class="m-0 font-weight-bold text-secondary">Fecha de Expedición</label>
    {!! Form::date('fecha_expedicion', old('fecha_expedicion'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha de Expedición',
    ]) !!}
</div>
