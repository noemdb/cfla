<div>
    <fieldset {{ $editModeAssitHour ? null : 'disabled="disabled"' }} class="p-4">

        {{-- 'assit_turn_id','h','m','type' --}}

        @include('livewire.elements.messeges.oper_ok')

        <div class="container-fluid">

            <div class="row">

                <div class="col">
                    <div class="form-group">
                        {!! Form::label('h', $list_comment_assit_hour['h'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                        {!! Form::selectRange('h', 1, 24, old('h'), [
                            'wire:model.defer' => 'h',
                            'class' => 'form-control',
                            'id' => 'h',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                        @error('h')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('m', $list_comment_assit_hour['m'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                        {!! Form::selectRange('m', 0, 60, old('m'), [
                            'wire:model.defer' => 'm',
                            'class' => 'form-control',
                            'id' => 'm',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                        @error('m')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('type', $list_comment_assit_hour['type'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                    {!! Form::select('frecuency', [true => 'Entrada', false => 'Salida'], old('type'), [
                        'wire:model.defer' => 'type',
                        'class' => 'form-control',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    @error('type')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

            </div>

        </div>

    </fieldset>

    <div class="input-group p-2">
        @php $disabled = ($editModeAssitHour) ? null : 'disabled' @endphp
        {!! Form::button('Actualizar', [
            'wire:click' => 'save',
            'class' => 'form-control btn-update btn btn-primary',
            'id' => 'update',
            $disabled,
        ]) !!}
        <div class="input-group-prepend">
            @php $icon = '<i class="'.$icon_menus['edit'].'" aria-hidden="true"></i>'; @endphp
            {!! Form::button($icon, [
                'wire:click' => 'editModeAssitHourOn',
                'class' => 'input-group-text btn-warning',
                'id' => 'edit',
            ]) !!}
        </div>
    </div>

</div>
