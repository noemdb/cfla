<div class="form-group">
    <label for="vclassroom_id"
        class=" font-weight-bold m-0 small">{{ $comment_pevaluacion['vclassroom_id'] ?? '' }}</label>
    {!! Form::select('vclassroom_id', $list_classroom, old('vclassroom_id'), [
        'wire:model.defer' => 'vclassroom_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
    @error('vclassroom_id')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="url_classroom_virtual"
        class=" font-weight-bold m-0 small">{{ $comment_pevaluacion['url_classroom_virtual'] ?? '' }}</label>
    {!! Form::text('url_classroom_virtual', old('url_classroom_virtual'), [
        'wire:model.defer' => 'url_classroom_virtual',
        'class' => 'form-control',
        'placeholder' => $comment_pevaluacion['url_classroom_virtual'],
    ]) !!}
    @error('url_classroom_virtual')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="description" class=" font-weight-bold m-0 small">{{ $comment_pevaluacion['description'] ?? '' }}</label>
    {!! Form::text('description', old('description'), [
        'wire:model.defer' => 'description',
        'class' => 'form-control',
        'placeholder' => 'Descripción',
        'id' => 'Descripción',
    ]) !!}
    @error('description')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="objetivo" class=" font-weight-bold m-0 small">{{ $comment_pevaluacion['objetivo'] ?? '' }}</label>
    {!! Form::text('objetivo', old('objetivo'), [
        'wire:model.defer' => 'objetivo',
        'class' => 'form-control',
        'placeholder' => 'Objetivo',
        'id' => 'objetivo',
    ]) !!}
    @error('objetivo')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="observations"
        class=" font-weight-bold m-0 small">{{ $comment_pevaluacion['observations'] ?? '' }}</label>
    {!! Form::text('observations', old('observations'), [
        'wire:model.defer' => 'observations',
        'class' => 'form-control',
        'placeholder' => 'Observación',
        'id' => 'observations',
    ]) !!}
    @error('observations')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>
