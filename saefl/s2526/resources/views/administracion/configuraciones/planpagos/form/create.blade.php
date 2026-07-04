<div class="form-group">
    <label for="name" class="m-0">{{ $list_comment['name'] }}</label>
    {!! Form::text('name', old('name'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['name'],
        'required' => 'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="description" class="m-0">{{ $list_comment['description'] }}</label>
    {!! Form::text('description', old('description'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['description'],
        'required' => 'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="observations" class="m-0">{{ $list_comment['observations'] }}</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['observations'],
    ]) !!}
</div>
