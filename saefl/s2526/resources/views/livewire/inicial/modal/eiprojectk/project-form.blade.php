<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                @php
                    $name = 'grado_id';
                    $model = 'eiprojectk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_grado, old($model), [
                    'wire:model' => $model,
                    'class' => 'form-control',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                @php
                    $name = 'seccion_id';
                    $model = 'eiprojectk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_seccion, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['finicial'] }} *</label>
                <input type="date" class="form-control @error('eiprojectk.finicial') is-invalid @enderror"
                    wire:model.defer="eiprojectk.finicial">
                @error('eiprojectk.finicial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['ffinal'] }} *</label>
                <input type="date" class="form-control @error('eiprojectk.ffinal') is-invalid @enderror"
                    wire:model.defer="eiprojectk.ffinal">
                @error('eiprojectk.ffinal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['tiempo_ejecucion'] }} *</label>
                <input type="number" min="1"
                    class="form-control @error('eiprojectk.tiempo_ejecucion') is-invalid @enderror"
                    wire:model.defer="eiprojectk.tiempo_ejecucion" placeholder="Número de semanas">
                @error('eiprojectk.tiempo_ejecucion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment['diagnostico'] }} *</label>
        <textarea class="form-control @error('eiprojectk.diagnostico') is-invalid @enderror"
            wire:model.defer="eiprojectk.diagnostico" rows="4"
            placeholder="Describa el diagnóstico inicial del proyecto..."></textarea>
        @error('eiprojectk.diagnostico')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mr-2" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>
            {{ $modalType === 'edit' ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</form>
