<div>
    <div class="p-2">
        <div class="d-flex justify-content-between py-2">
            <div>
                <div class="text-muted"><strong>Listado</strong> <span>de asistencia a los Servicios Ejecutados de Acción
                        Comunitaria.</span></div>
            </div>
            <div>
                <button class="btn btn-info" type="button" wire:click="create()">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Registrar Asistencia
                </button>
            </div>
        </div>

        <div>

            <div class="form-group">
                @php
                    $name = 'community_action_id';
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_community_actions, old($model), [
                    'wire:model' => $model,
                    'class' => 'form-control',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            @includeWhen($modeIndex, 'livewire.profesor.social-actions.community-hour.table.index')
            @includeWhen($modeCreate, 'livewire.profesor.social-actions.community-hour.form.create')

        </div>
    </div>
</div>
