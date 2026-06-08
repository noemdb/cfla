<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                @php
                    $name = 'grado_id';
                    $model = 'eievaluationk.' . $name;
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
                    $model = 'eievaluationk.' . $name;
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

    <div class="form-group">
        @php
            $name = 'lapso_id';
            $model = 'eievaluationk.' . $name;
        @endphp
        <label for="{{ $model }}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::select($model, $list_lapso, old($model), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'id' => $model,
            'placeholder' => 'Selecciones',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['finicial'] }} *</label>
                <input type="date" class="form-control @error('eievaluationk.finicial') is-invalid @enderror"
                    wire:model.defer="eievaluationk.finicial">
                @error('eievaluationk.finicial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['ffinal'] }} *</label>
                <input type="date" class="form-control @error('eievaluationk.ffinal') is-invalid @enderror"
                    wire:model.defer="eievaluationk.ffinal">
                @error('eievaluationk.ffinal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment['observaciones'] }} *</label>
        <textarea class="form-control @error('eievaluationk.observaciones') is-invalid @enderror"
            wire:model.defer="eievaluationk.observaciones" rows="4"
            placeholder="Describa las observaciones generales del grupo..."></textarea>
        @error('eievaluationk.observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment['asistencia'] }} *</label>
        <input type="text" class="form-control @error('eievaluationk.asistencia') is-invalid @enderror"
            wire:model.defer="eievaluationk.asistencia" placeholder="Descripción del control de asistencia">
        @error('eievaluationk.asistencia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment['recomendacion'] }}</label>
        <div class="alert alert-secondary">
            @if (isset($eievaluationk) && !empty($eievaluationk) && $eievaluationk->recomendacion)
                <p class="mb-0">{{ $eievaluationk->recomendacion }}</p>
                <p class="small text-muted mb-0">
                    <strong>Última actualización:</strong>
                    {{ $eievaluationk->updated_at ? $eievaluationk->updated_at->format('d/m/Y H:i') : 'N/A' }}
                </p>
            @else
                <p class="mb-0">No hay recomendaciones del coordinador de evaluación.</p>
            @endif
        </div>
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
