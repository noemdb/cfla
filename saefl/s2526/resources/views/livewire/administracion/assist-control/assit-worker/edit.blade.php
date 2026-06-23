<fieldset {{ $editModeAssitWorker ? null : 'disabled="disabled"' }}>

    {{-- 'name','number_turn','description','observations','frecuency','status' --}}

    <div class="container-fluid">

        <div class="row">

            <div class="col">
                <div class="form-group">
                    {!! Form::label('assit_schedule_id', $list_comment_user['assit_schedule_id'], [
                        'class' => 'font-weight-bold text-muted pb-0 mb-0',
                    ]) !!}
                    {!! Form::select('assit_schedule_id', $list_assit_schedule, old('assit_schedule_id'), [
                        'wire:model.defer' => 'assit_schedule_id',
                        'class' => 'form-control',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    @error('assit_schedule_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

        </div>

    </div>

</fieldset>

@php $disabled = ($editModeAssitWorker) ? null : 'disabled' @endphp
{!! Form::button('Actualizar', [
    'wire:click' => 'update()',
    'class' => 'form-control btn-update btn btn-primary',
    'id' => 'update',
    $disabled,
]) !!}
