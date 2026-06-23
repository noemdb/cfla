<fieldset {{ $editModeAssitSchedule || $createModeAssitSchedule ? null : 'disabled="disabled"' }}>

    {{-- 'name','number_turn','description','observations','frecuency','status' --}}

    <div class="container-fluid">

        <div class="row">

            <div class="col">
                <div class="form-group">
                    {!! Form::label('name', $list_comment['name'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                    {!! Form::text('name', old('name'), [
                        'wire:model.defer' => 'name',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['name'],
                    ]) !!}
                    @error('name')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    {!! Form::label('description', $list_comment['description'], [
                        'class' => 'font-weight-bold text-muted pb-0 mb-0',
                    ]) !!}
                    {!! Form::textarea('description', old('description'), [
                        'wire:model.defer' => 'description',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['description'],
                        'id' => 'description',
                        'rows' => '2',
                    ]) !!}
                    @error('description')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    {!! Form::label('frecuency', $list_comment['frecuency'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                    {!! Form::select('frecuency', ['SEMANAL' => 'SEMANAL', 'QUINCENAL' => 'QUINCENAL'], old('frecuency'), [
                        'wire:model.defer' => 'frecuency',
                        'class' => 'form-control',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    @error('frecuency')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    {!! Form::label('number_turn', $list_comment['number_turn'], [
                        'class' => 'font-weight-bold text-muted pb-0 mb-0',
                    ]) !!}
                    {!! Form::selectRange('number_turn', 1, 4, old('number_turn'), [
                        'wire:model.defer' => 'number_turn',
                        'class' => 'form-control',
                        'id' => 'number_turn',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    @error('number_turn')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    {!! Form::label('observations', $list_comment['observations'], [
                        'class' => 'font-weight-bold text-muted pb-0 mb-0',
                    ]) !!}
                    {!! Form::textarea('observations', old('observations'), [
                        'wire:model.defer' => 'observations',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['observations'],
                        'id' => 'observations',
                        'rows' => '2',
                    ]) !!}
                    @error('observations')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    {!! Form::label('status', $list_comment['status'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                    {!! Form::select('status', ['true' => 'Activo', 'false' => 'Desactivo'], old('status'), [
                        'wire:model.defer' => 'status',
                        'class' => 'form-control',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    @error('status')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

        </div>

    </div>

</fieldset>

<div class="input-group">
    @php $disabled = ($editMode || $createMode) ? null : 'disabled' @endphp
    {!! Form::button('Guardar', [
        'wire:click' => 'save',
        'class' => 'form-control btn-update btn btn-primary',
        'id' => 'update',
        $disabled,
    ]) !!}
    <div class="input-group-prepend">
        @php $icon = '<i class="'.$icon_menus['edit'].'" aria-hidden="true"></i>'; @endphp
        {!! Form::button($icon, [
            'wire:click' => 'editModeOn',
            'class' => 'input-group-text btn-warning',
            'id' => 'edit',
        ]) !!}
    </div>
    <div class="input-group-prepend">
        @php $icon = '<i class="'.$icon_menus['nuevo'].'" aria-hidden="true"></i>'; @endphp
        {!! Form::button($icon, [
            'wire:click' => 'createModeOn',
            'class' => 'input-group-text btn-primary',
            'id' => 'edit',
        ]) !!}
    </div>
</div>
