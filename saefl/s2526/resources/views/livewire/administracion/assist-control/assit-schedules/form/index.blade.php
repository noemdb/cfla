<fieldset {{ $editModeAssitSchedule || $createModeAssitSchedule ? null : 'disabled="disabled"' }}>

    @include('livewire.administracion.assist-control.assit-schedules.form.fields')

</fieldset>

<div class="input-group">
    @php $disabled = ($editModeAssitSchedule || $createModeAssitSchedule) ? null : 'disabled' @endphp
    {!! Form::button('Guardar', [
        'wire:click' => 'save',
        'class' => 'form-control btn-update btn btn-primary',
        'id' => 'update',
        $disabled,
    ]) !!}
    <div class="input-group-prepend">
        @php $icon = '<i class="'.$icon_menus['edit'].'" aria-hidden="true"></i>'; @endphp
        {!! Form::button($icon, [
            'wire:click' => 'editModeAssitScheduleOn',
            'class' => 'input-group-text btn-warning',
            'id' => 'edit',
        ]) !!}
    </div>
    <div class="input-group-prepend">
        @php $icon = '<i class="'.$icon_menus['nuevo'].'" aria-hidden="true"></i>'; @endphp
        {!! Form::button($icon, [
            'wire:click' => 'createModeAssitScheduleOn',
            'class' => 'input-group-text btn-primary',
            'id' => 'edit',
        ]) !!}
    </div>
</div>
