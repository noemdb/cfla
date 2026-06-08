<div>

    @include('livewire.elements.messeges.oper_ok')

    <fieldset {{ $editModeAssitWeek || $createModeAssitWeek ? null : 'disabled="disabled"' }}>

        @include('livewire.administracion.assist-control.assit-week.form.fields')

    </fieldset>

    <div class="input-group">
        @php $disabled = ($editModeAssitWeek || $createModeAssitWeek) ? null : 'disabled' @endphp
        {!! Form::button('Guardar', [
            'wire:click' => 'saveAssitWeek',
            'class' => 'form-control btn-update btn btn-primary',
            'id' => 'update',
            $disabled,
        ]) !!}
        <div class="input-group-prepend">
            @php $icon = '<i class="'.$icon_menus['edit'].'" aria-hidden="true"></i>'; @endphp
            {!! Form::button($icon, [
                'wire:click' => 'setMode("editON",' . $assit_week_id . ')',
                'class' => 'input-group-text btn-warning',
                'id' => 'edit',
            ]) !!}
        </div>
        <div class="input-group-prepend">
            @php $icon = '<i class="'.$icon_menus['nuevo'].'" aria-hidden="true"></i>'; @endphp
            {!! Form::button($icon, [
                'wire:click' => 'setMode("createON")',
                'class' => 'input-group-text btn-primary',
                'id' => 'create',
            ]) !!}
        </div>
    </div>

</div>
