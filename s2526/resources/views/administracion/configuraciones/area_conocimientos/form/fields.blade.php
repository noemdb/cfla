<div class=" d-block">

    <label for="peducativo_id"
        class="font-weight-bold text-secondary m-0">{{ $list_comment_area['peducativo_id'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('peducativo_id', $list_peducativo, old('peducativo_id'), [
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>

    <label for="pestudio_id"
        class="font-weight-bold text-secondary m-0">{{ $list_comment_area['pestudio_id'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('pestudio_id', $list_pestudio, old('pestudio_id'), [
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>

    @admin
        <div class="row pb-2">
            <div class="col">
                <label for="leader_id" class="m-0 font-weight-bold text-secondary">Jefe del Área</label>
                {!! Form::select('leader_id', $user_list, old('leader_id'), [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
    @endadmin

    <label for="name" class="font-weight-bold text-secondary m-0">{{ $list_comment_area['name'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('name', old('name'), [
            'class' => 'form-control',
            'placeholder' => $list_comment_area['name'],
            'required',
        ]) !!}
    </div>
    <label for="code" class="font-weight-bold text-secondary m-0">{{ $list_comment_area['code'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('code', old('code'), [
            'class' => 'form-control',
            'placeholder' => $list_comment_area['code'],
            'required',
        ]) !!}
    </div>
    <label for="code_sm" class="font-weight-bold text-secondary m-0">{{ $list_comment_area['code_sm'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('code_sm', old('code_sm'), [
            'class' => 'form-control',
            'placeholder' => $list_comment_area['code_sm'],
            'required',
        ]) !!}
    </div>
    <label for="description"
        class="font-weight-bold text-secondary m-0">{{ $list_comment_area['description'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('description', old('description'), [
            'class' => 'form-control',
            'placeholder' => $list_comment_area['description'],
        ]) !!}
    </div>
    <label for="observations"
        class="font-weight-bold text-secondary m-0">{{ $list_comment_area['observations'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('observations', old('observations'), [
            'class' => 'form-control',
            'placeholder' => $list_comment_area['observations'],
        ]) !!}
    </div>
    <label for="order" class="font-weight-bold text-secondary m-0">{{ $list_comment_area['order'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::selectRange('order', 1, 15, old('order'), [
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
    <label for="enable_academic_index"
        class="font-weight-bold text-secondary m-0">{{ $list_comment_area['enable_academic_index'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('enable_academic_index', ['true' => 'SI', 'false' => 'NO'], old('enable_academic_index'), [
            'class' => 'form-control',
            'required',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>


</div>


{{--
const COLUMN_COMMENTS = [
'pestudio_id' => 'Plan Estudio',
'name' => 'Nombre',
'code' => 'Código',
'code_sm' => 'Abreviatura',
'description' => 'Descripción',
'observations' => 'Observaciones',
'order' => 'Número de orden de presentación',
'enable_academic_index' => 'Tomada en cuenta para índice o promedio académico',
];
--}}
