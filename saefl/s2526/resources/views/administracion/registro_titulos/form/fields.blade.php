<div class="row">
    <div class="col-6">
        <label for="pestudio_id" class="m-0 font-weight-bold text-muted">{{ $list_comment['pestudio_id'] ?? '' }}</label>
        <div class="input-group mb-3">
            {!! Form::select('pestudio_id', $list_pestudios, old('pestudio_id'), [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
    </div>
    <div class="col-6">
        <label for="grado_id" class="m-0 font-weight-bold text-muted">{{ $list_comment['grado_id'] ?? '' }}</label>
        <div class="input-group mb-3">
            {!! Form::select('grado_id', $list_grado, old('grado_id'), [
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-6">
        <label for="funcionario_ci"
            class="m-0 font-weight-bold text-muted">{{ $list_comment['funcionario_ci'] ?? '' }}</label>
        {!! Form::text('funcionario_ci', old('funcionario_ci'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['funcionario_ci'],
            'id' => 'funcionario_ci',
        ]) !!}
    </div>
    <div class="col-6">
        <label for="funcionario_name"
            class="m-0 font-weight-bold text-muted">{{ $list_comment['funcionario_name'] ?? '' }}</label>
        {!! Form::text('funcionario_name', old('funcionario_name'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['funcionario_name'],
            'id' => 'funcionario_name',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="name" class="m-0 font-weight-bold text-muted">{{ $list_comment['name'] ?? '' }}</label>
    {!! Form::text('name', old('name'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['name'],
        'id' => 'name',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="fecha_egreso" class="m-0 font-weight-bold text-muted">{{ $list_comment['fecha_egreso'] ?? '' }}</label>
    {!! Form::date('fecha_egreso', old('fecha_egreso'), [
        'class' => 'form-control',
        'id' => 'fecha_egreso',
        'required',
        'id' => 'fecha_egreso',
    ]) !!}
</div>

<div class="form-group">
    <label for="code" class="m-0 font-weight-bold text-muted">{{ $list_comment['code'] ?? '' }}</label>
    {!! Form::text('code', old('code'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['code'],
        'id' => 'code',
        'required',
    ]) !!}
</div>

<label for="tipo" class="m-0 font-weight-bold text-muted">{{ $list_comment['tipo'] ?? '' }}</label>
<div class="input-group mb-3">
    {!! Form::select('tipo', $list_tipo, old('tipo'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="tevaluacion_id" class="m-0 font-weight-bold text-muted">{{ $list_comment['tevaluacion_id'] ?? '' }}</label>
<div class="input-group mb-3">
    {!! Form::select('tevaluacion_id', $list_tipo_evaluacion, old('tevaluacion_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>
