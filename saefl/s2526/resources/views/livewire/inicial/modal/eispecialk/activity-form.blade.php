<form wire:submit.prevent="saveActivity">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_activities['pevaluacion_id'] }} *</label>
                <select class="form-control @error('eispecialact.pevaluacion_id') is-invalid @enderror" 
                        wire:model.defer="eispecialact.pevaluacion_id">
                    <option value="">Seleccione un área de aprendizaje</option>
                    @foreach($list_pevaluacion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('eispecialact.pevaluacion_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_activities['order'] }}</label>
                <input type="number" min="1" 
                       class="form-control @error('eispecialact.order') is-invalid @enderror" 
                       wire:model.defer="eispecialact.order" 
                       placeholder="Orden de presentación">
                @error('eispecialact.order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_activities['componente'] }} *</label>
        <input type="text" class="form-control @error('eispecialact.componente') is-invalid @enderror" 
               wire:model.defer="eispecialact.componente" 
               placeholder="Componente del área de aprendizaje">
        @error('eispecialact.componente')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_activities['objetivo'] }} *</label>
        <textarea class="form-control @error('eispecialact.objetivo') is-invalid @enderror" 
                  wire:model.defer="eispecialact.objetivo" 
                  rows="3" 
                  placeholder="Objetivo a alcanzar"></textarea>
        @error('eispecialact.objetivo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_activities['aprendizaje_esperado'] }} *</label>
        <textarea class="form-control @error('eispecialact.aprendizaje_esperado') is-invalid @enderror" 
                  wire:model.defer="eispecialact.aprendizaje_esperado" 
                  rows="3" 
                  placeholder="Descripción del aprendizaje esperado"></textarea>
        @error('eispecialact.aprendizaje_esperado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_activities['indicadores'] }} *</label>
        <textarea class="form-control @error('eispecialact.indicadores') is-invalid @enderror" 
                  wire:model.defer="eispecialact.indicadores" 
                  rows="3" 
                  placeholder="Indicadores de evaluación"></textarea>
        @error('eispecialact.indicadores')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_activities['linea_investigacion'] }}</label>
                <input type="text" class="form-control @error('eispecialact.linea_investigacion') is-invalid @enderror" 
                       wire:model.defer="eispecialact.linea_investigacion" 
                       placeholder="Línea de investigación">
                @error('eispecialact.linea_investigacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_activities['enfasis_curriculares'] }}</label>
                <input type="text" class="form-control @error('eispecialact.enfasis_curriculares') is-invalid @enderror" 
                       wire:model.defer="eispecialact.enfasis_curriculares" 
                       placeholder="Énfasis curriculares">
                @error('eispecialact.enfasis_curriculares')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mr-2" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>
            {{ $eispecialact_id ? 'Actualizar' : 'Guardar' }} Actividad
        </button>
    </div>
</form>
