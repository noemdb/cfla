<div class=" d-block">
    <label for="pestudio_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['pestudio_id'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('pestudio_id', $list_pestudio, old('pestudio_id'), [
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
    <label for="code" class="font-weight-bold text-secondary m-0">{{ $list_comment['code'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('code', old('code'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['code'],
            'required',
        ]) !!}
    </div>
    <label for="code_sm" class="font-weight-bold text-secondary m-0">{{ $list_comment['code_sm'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('code_sm', old('code_sm'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['code_sm'],
            'required',
        ]) !!}
    </div>
    <label for="description"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('description', old('description'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['description'],
            'required',
        ]) !!}
    </div>
    {{-- <label for="order" class="font-weight-bold text-secondary m-0">{{$list_comment['order'] ?? ''}}</label>
    <div class="form-group">
        {!! Form::selectRange('order', 1, 5,old('order'),['class'=>'form-control','placeholder'=>'Seleccione']) !!}
    </div>
     --}}

    <label for="hour_social"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['hour_social'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::number('hour_social', old('hour_social'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['hour_social'],
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
