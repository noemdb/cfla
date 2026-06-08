<form wire:submit.prevent="saveSummary">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_summary['pevaluacion_id'] }} *</label>
                <select class="form-control @error('eiprojectsummary.pevaluacion_id') is-invalid @enderror" 
                        wire:model.defer="eiprojectsummary.pevaluacion_id">
                    <option value="">Seleccione un área de aprendizaje</option>
                    @foreach($list_pevaluacion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('eiprojectsummary.pevaluacion_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_summary['order'] }}</label>
                <input type="number" min="1" 
                       class="form-control @error('eiprojectsummary.order') is-invalid @enderror" 
                       wire:model.defer="eiprojectsummary.order" 
                       placeholder="Orden de presentación">
                @error('eiprojectsummary.order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_summary['componente'] }} *</label>
        <input type="text" class="form-control @error('eiprojectsummary.componente') is-invalid @enderror" 
               wire:model.defer="eiprojectsummary.componente" 
               placeholder="Componente del área de aprendizaje">
        @error('eiprojectsummary.componente')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_summary['objetivo'] }} *</label>
        <textarea class="form-control @error('eiprojectsummary.objetivo') is-invalid @enderror" 
                  wire:model.defer="eiprojectsummary.objetivo" 
                  rows="3" 
                  placeholder="Objetivo a alcanzar"></textarea>
        @error('eiprojectsummary.objetivo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_summary['aprendizaje_esperado'] }} *</label>
        <textarea class="form-control @error('eiprojectsummary.aprendizaje_esperado') is-invalid @enderror" 
                  wire:model.defer="eiprojectsummary.aprendizaje_esperado" 
                  rows="3" 
                  placeholder="Descripción del aprendizaje esperado"></textarea>
        @error('eiprojectsummary.aprendizaje_esperado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_summary['indicadores'] }} *</label>
        <textarea class="form-control @error('eiprojectsummary.indicadores') is-invalid @enderror" 
                  wire:model.defer="eiprojectsummary.indicadores" 
                  rows="3" 
                  placeholder="Indicadores de evaluación"></textarea>
        @error('eiprojectsummary.indicadores')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_summary['linea_investigacion'] }}</label>
                <input type="text" class="form-control @error('eiprojectsummary.linea_investigacion') is-invalid @enderror" 
                       wire:model.defer="eiprojectsummary.linea_investigacion" 
                       placeholder="Línea de investigación">
                @error('eiprojectsummary.linea_investigacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_summary['enfasis_curriculares'] }}</label>
                <input type="text" class="form-control @error('eiprojectsummary.enfasis_curriculares') is-invalid @enderror" 
                       wire:model.defer="eiprojectsummary.enfasis_curriculares" 
                       placeholder="Énfasis curriculares">
                @error('eiprojectsummary.enfasis_curriculares')
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
            {{ $eiprojectsummary_id ? 'Actualizar' : 'Guardar' }} Resumen
        </button>
    </div>
</form>
