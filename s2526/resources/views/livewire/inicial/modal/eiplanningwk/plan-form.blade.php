<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="form-group">
                    @php
                        $name = 'grado_id';
                        $model = 'eiplanningwk.' . $name;
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
                @error('eiplanningwk.grado_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <div class="form-group">
                    @php
                        $name = 'seccion_id';
                        $model = 'eiplanningwk.' . $name;
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
                @error('eiplanningwk.seccion_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment['eiprojectk_id'] }}</label>
        <select class="form-control @error('eiplanningwk.eiprojectk_id') is-invalid @enderror"
            wire:model.defer="eiplanningwk.eiprojectk_id">
            <option value="">Seleccione un proyecto (opcional)</option>
            @foreach ($list_eiprojectk as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        @error('eiplanningwk.eiprojectk_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['finicial'] }} *</label>
                <input type="date" class="form-control @error('eiplanningwk.finicial') is-invalid @enderror"
                    wire:model.defer="eiplanningwk.finicial">
                @error('eiplanningwk.finicial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['ffinal'] }} *</label>
                <input type="date" class="form-control @error('eiplanningwk.ffinal') is-invalid @enderror"
                    wire:model.defer="eiplanningwk.ffinal">
                @error('eiplanningwk.ffinal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment['tiempo_ejecucion'] }} *</label>
                <input type="number" min="1"
                    class="form-control @error('eiplanningwk.tiempo_ejecucion') is-invalid @enderror"
                    wire:model.defer="eiplanningwk.tiempo_ejecucion" placeholder="Número de semanas">
                @error('eiplanningwk.tiempo_ejecucion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment['diagnostico'] }} *</label>
        <textarea class="form-control @error('eiplanningwk.diagnostico') is-invalid @enderror"
            wire:model.defer="eiplanningwk.diagnostico" rows="4"
            placeholder="Describa el diagnóstico inicial del grupo..."></textarea>
        @error('eiplanningwk.diagnostico')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">

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
