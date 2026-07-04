<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                @php
                    $name = 'grado_id';
                    $model = 'eiplanningbwk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
                    $model = 'eiplanningbwk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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

    <div class="col">
        <div class="form-group">
            @php
                $name = 'eiprojectk_id';
                $model = 'eiplanningbwk.' . $name;
            @endphp
            <label for="{{ $model }}"
                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::select($model, $list_eiprojectk, old($model), [
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

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['finicial'] }} *</label>
                <input type="date" class="form-control @error('eiplanningbwk.finicial') is-invalid @enderror"
                    wire:model.defer="eiplanningbwk.finicial">
                @error('eiplanningbwk.finicial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['ffinal'] }} *</label>
                <input type="date" class="form-control @error('eiplanningbwk.ffinal') is-invalid @enderror"
                    wire:model.defer="eiplanningbwk.ffinal">
                @error('eiplanningbwk.ffinal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['tiempo_ejecucion'] }} *</label>
                <input type="number" min="1"
                    class="form-control @error('eiplanningbwk.tiempo_ejecucion') is-invalid @enderror"
                    wire:model.defer="eiplanningbwk.tiempo_ejecucion" placeholder="Número de quincenas">
                @error('eiplanningbwk.tiempo_ejecucion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment['diagnostico'] }} *</label>
        <textarea class="form-control @error('eiplanningbwk.diagnostico') is-invalid @enderror"
            wire:model.defer="eiplanningbwk.diagnostico" rows="4"
            placeholder="Describa el diagnóstico inicial del grupo..."></textarea>
        @error('eiplanningbwk.diagnostico')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="alert alert-secondary">
        @if (isset($eiplanningwk) && !empty($eiplanningwk) && $eiplanningwk->observacion)
            <p class="mb-0">{{ $eiplanningwk->observacion }}</p>
            <p class="small text muted mb-0">
                <strong>Última actualización:</strong>
                {{ $eiplanningwk->updated_at ? $eiplanningwk->updated_at->format('d/m/Y H:i') : 'N/A' }}
            </p>
        @else
            <p class="mb-0">No hay observaciones del coordinador de evaluación.</p>
        @endif
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mr-2" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save mr-1"></i>
            {{ $modalType === 'edit' ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</form>
