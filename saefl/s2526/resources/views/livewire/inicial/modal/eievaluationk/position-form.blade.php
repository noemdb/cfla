<form wire:submit.prevent="savePosition">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_position['pevaluacion_id'] }} *</label>
                <select class="form-control @error('eievaluationp.pevaluacion_id') is-invalid @enderror" 
                        wire:model.defer="eievaluationp.pevaluacion_id">
                    <option value="">Seleccione un área de aprendizaje</option>
                    @foreach($list_pevaluacion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('eievaluationp.pevaluacion_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_position['order'] }}</label>
                <input type="number" min="1" 
                       class="form-control @error('eievaluationp.order') is-invalid @enderror" 
                       wire:model.defer="eievaluationp.order" 
                       placeholder="Orden de presentación">
                @error('eievaluationp.order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_position['fecha'] }}</label>
                <input type="text" class="form-control @error('eievaluationp.fecha') is-invalid @enderror" 
                       wire:model.defer="eievaluationp.fecha" 
                       placeholder="Fecha de la evaluación">
                @error('eievaluationp.fecha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_position['nombre_ninos'] }}</label>
                <input type="text" class="form-control @error('eievaluationp.nombre_ninos') is-invalid @enderror" 
                       wire:model.defer="eievaluationp.nombre_ninos" 
                       placeholder="Nombres de los niños evaluados">
                @error('eievaluationp.nombre_ninos')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_position['componente'] }}</label>
        <input type="text" class="form-control @error('eievaluationp.componente') is-invalid @enderror" 
               wire:model.defer="eievaluationp.componente" 
               placeholder="Componente del área de aprendizaje">
        @error('eievaluationp.componente')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_position['aprendizaje_alcanzado'] }}</label>
        <textarea class="form-control @error('eievaluationp.aprendizaje_alcanzado') is-invalid @enderror" 
                  wire:model.defer="eievaluationp.aprendizaje_alcanzado" 
                  rows="3" 
                  placeholder="Descripción del aprendizaje alcanzado"></textarea>
        @error('eievaluationp.aprendizaje_alcanzado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_position['indicadores'] }}</label>
        <textarea class="form-control @error('eievaluationp.indicadores') is-invalid @enderror" 
                  wire:model.defer="eievaluationp.indicadores" 
                  rows="3" 
                  placeholder="Indicadores de evaluación"></textarea>
        @error('eievaluationp.indicadores')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_position['instrumento'] }}</label>
                <input type="text" class="form-control @error('eievaluationp.instrumento') is-invalid @enderror" 
                       wire:model.defer="eievaluationp.instrumento" 
                       placeholder="Instrumento de evaluación utilizado">
                @error('eievaluationp.instrumento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_position['observacion'] }}</label>
                <input type="text" class="form-control @error('eievaluationp.observacion') is-invalid @enderror" 
                       wire:model.defer="eievaluationp.observacion" 
                       placeholder="Observaciones adicionales">
                @error('eievaluationp.observacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-secondary mr-2" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>
            {{ $eievaluationp_id ? 'Actualizar' : 'Guardar' }} Posición
        </button>
    </div>
</form>
