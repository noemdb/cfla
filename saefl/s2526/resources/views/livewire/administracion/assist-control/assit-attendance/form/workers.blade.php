<fieldset {{ $editWorkerModeAssitSchedules ? null : 'disabled="disabled"' }}>

    {{-- 'name','number_turn','description','observations','frecuency','status' --}}

    <div class=" alert bg-secondary text-light font-weight-bold">
        {{ $worker_fullname ?? null }} || <small>CI: {{ $worker_number_id ?? null }}</small>
        @if (isset($ident) && $ident)
            <small>|| Ident: {{ $ident ?? null }}</small>
        @endif
        @if (isset($work_id) && $work_id)
            <small>|| Work ID: {{ $work_id ?? null }}</small>
        @endif

        <button type="button" class="close" wire:click='closeEditMode()'>
            <span aria-hidden="true">×</span>
        </button>
    </div>

    <div class="px-2">
        @include('admin.elements.messeges.oper_ok')
    </div>

    <fieldset {{ $updated ? 'disabled="disabled"' : null }}>

        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('number_id', $list_comment_user['number_id'], [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::text('number_id', old('number_id'), [
                            'wire:model.defer' => 'number_id',
                            'class' => 'form-control',
                            'placeholder' => $list_comment_user['number_id'],
                            'id' => 'number_id',
                            'required',
                        ]) !!}
                        @error('number_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('worker_order', $list_comment_user['worker_order'], [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::selectRange('worker_order', 1, 100, old('worker_order'), [
                            'wire:model.defer' => 'worker_order',
                            'class' => 'form-control',
                            'required',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                        @error('worker_order')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('work_id', $list_comment_user['work_id'] ?? 'Work ID', [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::text('work_id', old('work_id'), [
                            'wire:model.defer' => 'work_id',
                            'class' => 'form-control',
                            'placeholder' => 'Work ID',
                            'id' => 'work_id',
                        ]) !!}
                        @error('work_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('ident', $list_comment_user['ident'] ?? 'Identificación', [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::text('ident', old('ident'), [
                            'wire:model.defer' => 'ident',
                            'class' => 'form-control',
                            'placeholder' => 'Identificación',
                            'id' => 'ident',
                        ]) !!}
                        @error('ident')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('card_id', $list_comment_user['card_id'] ?? 'Card ID', [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::text('card_id', old('card_id'), [
                            'wire:model.defer' => 'card_id',
                            'class' => 'form-control',
                            'placeholder' => 'Card ID',
                            'id' => 'card_id',
                        ]) !!}
                        @error('card_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('status_schedule', 'Estado Horario', ['class' => 'font-weight-bold text-muted pb-0 mb-0']) !!}
                        {!! Form::select('status_schedule', [1 => 'Activo', 0 => 'Inactivo'], old('status_schedule'), [
                            'wire:model.defer' => 'status_schedule',
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione estado',
                        ]) !!}
                        @error('status_schedule')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('firstname', $list_comment_user['firstname'], [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::text('firstname', old('firstname'), [
                            'wire:model.defer' => 'firstname',
                            'class' => 'form-control',
                            'placeholder' => $list_comment_user['firstname'],
                            'id' => 'firstname',
                            'required',
                        ]) !!}
                        @error('firstname')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('lastname', $list_comment_user['lastname'], [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::text('lastname', old('lastname'), [
                            'wire:model.defer' => 'lastname',
                            'class' => 'form-control',
                            'placeholder' => $list_comment_user['lastname'],
                            'id' => 'lastname',
                            'required',
                        ]) !!}
                        @error('lastname')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('cargo_id', $list_comment_user['cargo_id'], [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::select('cargo_id', $list_cargos, old('cargo_id'), [
                            'wire:model.defer' => 'cargo_id',
                            'class' => 'form-control',
                            'placeholder' => 'Cargo que ejerce',
                        ]) !!}
                        @error('cargo_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('assit_schedule_id', $list_comment_user['assit_schedule_id'], [
                            'class' => 'font-weight-bold text-muted pb-0 mb-0',
                        ]) !!}
                        {!! Form::select('assit_schedule_id', $list_assit_schedule, old('assit_schedule_id'), [
                            'wire:model.defer' => 'assit_schedule_id',
                            'class' => 'form-control',
                            'placeholder' => 'Horario de asistencia',
                        ]) !!}
                        @error('assit_schedule_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

    </fieldset>

</fieldset>

<fieldset {{ $updated ? 'disabled="disabled"' : null }}>
    @php $disabled = ($editWorkerModeAssitSchedules) ? null : 'disabled' @endphp
    {!! Form::button('Actualizar', [
        'wire:click' => 'updateWorker()',
        'class' => 'form-control btn-update btn btn-primary',
        'id' => 'update',
        $disabled,
    ]) !!}
</fieldset>
