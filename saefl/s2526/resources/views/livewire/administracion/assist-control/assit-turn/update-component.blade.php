<div>
    <fieldset {{ $editModeAssitTurn ? null : 'disabled="disabled"' }} class="p-4">

        {{-- 'assit_day_id','name','number' --}}

        @include('livewire.elements.messeges.oper_ok')

        <div class="container-fluid">

            <div class="row">

                <div class="col">
                    <div class="form-group">
                        {!! Form::label('name', $list_comment_assit_turn['name'], ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                        {!! Form::text('name', old('name'), [
                            'wire:model.defer' => 'name',
                            'class' => 'form-control',
                            'placeholder' => $list_comment_assit_turn['name'],
                        ]) !!}
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        {!! Form::label('number', $list_comment_assit_turn['number'], [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::selectRange('number', 1, 7, old('number'), [
                            'wire:model.defer' => 'number',
                            'class' => 'form-control',
                            'id' => 'number',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                        @error('number')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

        </div>

    </fieldset>

    <div class="input-group p-2">
        @php $disabled = ($editModeAssitTurn) ? null : 'disabled' @endphp
        {!! Form::button('Actualizar', [
            'wire:click' => 'save',
            'class' => 'form-control btn-update btn btn-primary',
            'id' => 'update',
            $disabled,
        ]) !!}
        <div class="input-group-prepend">
            @php $icon = '<i class="'.$icon_menus['edit'].'" aria-hidden="true"></i>'; @endphp
            {!! Form::button($icon, [
                'wire:click' => 'editModeAssitTurnOn',
                'class' => 'input-group-text btn-warning',
                'id' => 'edit',
            ]) !!}
        </div>
    </div>

</div>
