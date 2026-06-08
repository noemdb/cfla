<label for="peducativo_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['peducativo_id'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('peducativo_id', $list_peducativo, old('peducativo_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="code" class="font-weight-bold text-secondary m-0">{{ $list_comment['code'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('code', old('code'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['code'],
        'id' => 'code',
        'required',
    ]) !!}
</div>

<label for="code_oficial" class="font-weight-bold text-secondary m-0">{{ $list_comment['code_oficial'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('code_oficial', old('code_oficial'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['code_oficial'],
        'id' => 'code_oficial',
        'required',
    ]) !!}
</div>

<label for="name" class="font-weight-bold text-secondary m-0">{{ $list_comment['name'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('name', old('name'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['name'],
        'id' => 'name',
        'required',
    ]) !!}
</div>

<label for="order" class="font-weight-bold text-secondary m-0">{{ $list_comment['order'] ?? '' }}</label>
<div class="form-group">
    {!! Form::selectRange('order', 1, 10, old('order'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>
<label for="description" class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('description', old('description'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['description'],
        'id' => 'description',
        'required',
    ]) !!}
</div>
<label for="description_aux"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['description_aux'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('description_aux', old('description_aux'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['description_aux'],
        'id' => 'description_aux',
    ]) !!}
</div>
<label for="mention" class="font-weight-bold text-secondary m-0">{{ $list_comment['mention'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('mention', old('mention'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['mention'],
        'id' => 'mention',
    ]) !!}
</div>
<label for="status_build_promotion"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_build_promotion'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('status_build_promotion', ['true' => 'SI', 'false' => 'NO'], old('status_build_promotion'), [
        'class' => 'form-control',
        'required',
        'id' => 'status_build_promotion',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>
<label for="title" class="font-weight-bold text-secondary m-0">{{ $list_comment['title'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('title', old('title'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['description'],
        'id' => 'title',
    ]) !!}
</div>
<label for="scale" class="font-weight-bold text-secondary m-0">{{ $list_comment['scale'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('scale', ['1' => 'CUALITATIVA', '2' => 'NUMERICA'], old('scale'), [
        'class' => 'form-control',
        'id' => 'scale',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>
<label for="profile" class="font-weight-bold text-secondary m-0">{{ $list_comment['profile'] ?? '' }}</label>
<div class="form-group">
    {!! Form::textarea('profile', old('profile'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['profile'],
        'id' => 'profile',
        'required',
    ]) !!}
</div>
<label for="color" class="font-weight-bold text-secondary m-0">{{ $list_comment['color'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('color', ['info' => 'info', 'primary' => 'primary', 'success' => 'success'], old('color'), [
        'class' => 'form-control',
        'id' => 'color',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>
<label for="show_hr" class="font-weight-bold text-secondary m-0">{{ $list_comment['show_hr'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('show_hr', ['true' => 'SI', 'false' => 'NO'], old('show_hr'), [
        'class' => 'form-control',
        'id' => 'show_hr',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>
<label for="status_a_cualitative"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_a_cualitative'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('status_a_cualitative', ['true' => 'SI', 'false' => 'NO'], old('status_a_cualitative'), [
        'class' => 'form-control',
        'required',
        'id' => 'status_a_cualitative',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>
<label for="status_baremo"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_baremo'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('status_baremo', ['true' => 'SI', 'false' => 'NO'], old('status_baremo'), [
        'class' => 'form-control',
        'id' => 'status_baremo',
        'required',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>
<label for="status_active"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_active'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('status_active', ['true' => 'SI', 'false' => 'NO'], old('status_active'), [
        'class' => 'form-control',
        'id' => 'status_active',
        'required',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<label for="status_inscripcion_active"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_inscripcion_active'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('status_inscripcion_active', [true => 'SI', false => 'NO'], old('status_inscripcion_active'), [
        'class' => 'form-control',
        'id' => 'status_inscripcion_active',
        'required',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

</div>
<label for="status_carga_notas"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_carga_notas'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('status_carga_notas', ['true' => 'SI', 'false' => 'NO'], old('status_carga_notas'), [
        'class' => 'form-control',
        'id' => 'status_carga_notas',
        'required',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<label for="remision_resumen_final"
    class="m-0 font-weight-bold text-secondary">{{ $list_comment['remision_resumen_final'] ?? '' }}</label>
<div class="form-group pb-1">
    {!! Form::date('remision_resumen_final', old('remision_resumen_final'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha final',
    ]) !!}
</div>


<label for="fecha_informe_final"
    class="m-0 font-weight-bold text-secondary">{{ $list_comment['fecha_informe_final'] ?? '' }}</label>
<div class="form-group pb-1">
    {!! Form::date('fecha_informe_final', old('fecha_informe_final'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha final',
    ]) !!}
</div>

<label for="fecha_certificacion"
    class="m-0 font-weight-bold text-secondary">{{ $list_comment['fecha_certificacion'] ?? '' }}</label>
<div class="form-group pb-1">
    {!! Form::date('fecha_certificacion', old('fecha_certificacion'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha final',
    ]) !!}
</div>

<label for="fecha_descriptivo"
    class="m-0 font-weight-bold text-secondary">{{ $list_comment['fecha_descriptivo'] ?? '' }}</label>
<div class="form-group pb-1">
    {!! Form::date('fecha_descriptivo', old('fecha_descriptivo'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha final',
    ]) !!}
</div>

<label for="fecha_promocion"
    class="m-0 font-weight-bold text-secondary">{{ $list_comment['fecha_promocion'] ?? '' }}</label>
<div class="form-group pb-1">
    {!! Form::date('fecha_promocion', old('fecha_promocion'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha final',
    ]) !!}
</div>

<label for="fecha_prosecucion"
    class="m-0 font-weight-bold text-secondary">{{ $list_comment['fecha_prosecucion'] ?? '' }}</label>
<div class="form-group pb-1">
    {!! Form::date('fecha_prosecucion', old('fecha_prosecucion'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha final',
    ]) !!}
</div>

<label class="mb-0 pb-0 mt-1 pt-1 font-weight-bold text-muted" for="status_socials">Requiere una cantidad determinada de
    <strong>Horas cumplidas</strong> en Labores Socio-Comunitaria Amigoniana.</label>
<div class="form-label-group pb-1">
    {!! Form::select('status_socials', [true => 'SI', false => 'NO'], old('status_socials'), [
        'class' => 'form-control',
        'placeholder' => 'Selecciones',
    ]) !!}
</div>

@admin
    <div class="row pb-2">
        <div class="col">
            <label for="manager_id" class="m-0 font-weight-bold text-secondary">Coordinador de Evaluación</label>
            {!! Form::select('manager_id', $user_list, old('manager_id'), [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
    </div>

    <div class="row pb-2">
        <div class="col">
            <label for="show_quantitative_indicators"
                class="font-weight-bold text-secondary m-0">{{ $list_comment['show_quantitative_indicators'] ?? '' }}</label>
            {!! Form::select(
                'show_quantitative_indicators',
                ['true' => 'SI', 'false' => 'NO'],
                old('show_quantitative_indicators'),
                ['class' => 'form-control', 'id' => 'show_quantitative_indicators', 'required', 'placeholder' => 'Seleccione'],
            ) !!}
        </div>
    </div>
@endadmin

<label for="description_trainig_component"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['description_trainig_component'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('description_trainig_component', old('description_trainig_component'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['description_trainig_component'],
        'id' => 'description_trainig_component',
    ]) !!}
</div>
