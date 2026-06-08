{{-- <div class="container"> --}}

<label for="name" class="font-weight-bold text-secondary m-0">{{ $list_comment['name'] ?? '' }}</label>
<div class="form-group">
    {!! Form::text('name', old('name'), [
        'class' => 'form-control',
        'placeholder' => $list_comment['name'],
        'id' => 'name',
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

<label for="order" class="font-weight-bold text-secondary m-0">{{ $list_comment['order'] ?? '' }}</label>
<div class="form-group">
    {!! Form::selectRange('order', 1, 5, old('order'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'id' => 'order',
    ]) !!}
</div>

<label for="status_active" class="font-weight-bold text-secondary m-0">{{ $list_comment['status_active'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('status_active', ['true' => 'SI', 'false' => 'NO'], old('status_active'), [
        'class' => 'form-control',
        'id' => 'status_active',
        'required',
        'placeholder' => 'Seleccione',
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
            <label for="assistant_id" class="m-0 font-weight-bold text-secondary">Coordinador Asistente</label>
            {!! Form::select('assistant_id', $user_list, old('assistant_id'), [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
    </div>

    <div class="row pb-2">
        <div class="col">
            <label for="deputy_id" class="m-0 font-weight-bold text-secondary">Coordinador Adjunto</label>
            {!! Form::select('deputy_id', $user_list, old('deputy_id'), [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
    </div>

    <label for="show_quantitative_indicators"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['show_quantitative_indicators'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select(
            'show_quantitative_indicators',
            ['true' => 'SI', 'false' => 'NO'],
            old('show_quantitative_indicators'),
            ['class' => 'form-control', 'id' => 'show_quantitative_indicators', 'required', 'placeholder' => 'Seleccione'],
        ) !!}
    </div>
@endadmin

{{-- </div> --}}
