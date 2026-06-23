<div class=" d-block">
    <label for="grado_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['grado_id'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('grado_id', $list_grado, old('grado_id'), [
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
    <label for="name" class="font-weight-bold text-secondary m-0">{{ $list_comment['name'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('name', old('name'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['name'],
            'required',
        ]) !!}
    </div>
    <label for="description" class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('description', old('description'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['description'],
        ]) !!}
    </div>
    <label for="amount_student"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['amount_student'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::selectRange('amount_student', 1, 50, old('amount_student'), [
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
    <label for="observation"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['observation'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('observation', old('observation'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['observation'],
        ]) !!}
    </div>
    <label for="comment_final"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['comment_final'] ?? '' }}</label>
    <div class="form-group">
        {{-- {!! Form::text('comment_final', old('comment_final'), ['class' => 'form-control','placeholder'=>$list_comment['comment_final']]); !!} --}}
        {!! Form::textarea('comment_final', old('comment_final'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['comment_final'],
            'id' => 'comment_final',
            'rows' => '4',
        ]) !!}
    </div>
    <label for="status_active"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['status_active'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('status_active', ['true' => 'SI', 'false' => 'NO'], old('status_active'), [
            'class' => 'form-control',
            'required',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>
</div>
<label for="status_inscription_affects"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_inscription_affects'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select(
        'status_inscription_affects',
        ['true' => 'SI', 'false' => 'NO'],
        old('status_inscription_affects'),
        ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione'],
    ) !!}
</div>
</div>


{{--
const COLUMN_COMMENTS = [
'grado_id' => 'Grado del Plan de Estudio',
'name' => 'Nombre',
'description' => 'Descripción',
'amount_student' => 'Cantidad de Estudiantes',
'observation' => 'Observaciones',
'status_active' => 'Estado'
];
--}}
