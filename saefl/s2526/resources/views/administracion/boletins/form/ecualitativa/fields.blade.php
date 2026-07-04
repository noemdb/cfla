<div class=" d-block">
    <label for="estudiant_id"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['estudiant_id'] ?? '' }}</label>
    <div class="form-group">
        {{ $ecualitativa->estudiant_id ?? '' }}
    </div>
    <label for="lapso_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['lapso_id'] ?? '' }}</label>
    <div class="form-group">
        {{ $ecualitativa->id ?? '' }}
    </div>
    <label for="lapso_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['lapso_id'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('lapso_id', $list_lapso, old('lapso_id'), [
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
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

    <label for="description"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('description', old('description'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['description'],
        ]) !!}
    </div>

    <label for="observations"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['observations'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('observations', old('observations'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['observations'],
        ]) !!}
    </div>

</div>
