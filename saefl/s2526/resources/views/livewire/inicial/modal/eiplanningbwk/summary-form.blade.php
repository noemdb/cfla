<form wire:submit.prevent="saveSummary">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_summary['pevaluacion_id'] }} *</label>
                <select class="form-control @error('eiplanningbwsummary.pevaluacion_id') is-invalid @enderror" 
                        wire:model.defer="eiplanningbwsummary.pevaluacion_id">
                    <option value="">Seleccione un área de aprendizaje</option>
                    @foreach($list_pevaluacion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('eiplanningbwsummary.pevaluacion_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_summary['order'] }}</label>
                <input type="number" min="1" 
                       class="form-control @error('eiplanningbwsummary.order') is-invalid @enderror" 
                       wire:model.defer="eiplanningbwsummary.order" 
                       placeholder="Orden de presentación">
                @error('eiplanningbwsummary.order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_summary['componente'] }} *</label>
        <input type="text" class="form-control @error('eiplanningbwsummary.componente') is-invalid @enderror" 
               wire:model.defer="eiplanningbwsummary.componente" 
               placeholder="Componente del área de aprendizaje">
        @error('eiplanningbwsummary.componente')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_summary['objetivo'] }} *</label>
        <textarea class="form-control @error('eiplanningbwsummary.objetivo') is-invalid @enderror" 
                  wire:model.defer="eiplanningbwsummary.objetivo" 
                  rows="3" 
                  placeholder="Objetivo a alcanzar"></textarea>
        @error('eiplanningbwsummary.objetivo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_summary['aprendizaje_esperado'] }} *</label>
        <textarea class="form-control @error('eiplanningbwsummary.aprendizaje_esperado') is-invalid @enderror" 
                  wire:model.defer="eiplanningbwsummary.aprendizaje_esperado" 
                  rows="3" 
                  placeholder="Descripción del aprendizaje esperado"></textarea>
        @error('eiplanningbwsummary.aprendizaje_esperado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label font-weight-bold">{{ $list_comment_summary['indicadores'] }} *</label>
        <textarea class="form-control @error('eiplanningbwsummary.indicadores') is-invalid @enderror" 
                  wire:model.defer="eiplanningbwsummary.indicadores" 
                  rows="3" 
                  placeholder="Indicadores de evaluación"></textarea>
        @error('eiplanningbwsummary.indicadores')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_summary['linea_investigacion'] }}</label>
                <input type="text" class="form-control @error('eiplanningbwsummary.linea_investigacion') is-invalid @enderror" 
                       wire:model.defer="eiplanningbwsummary.linea_investigacion" 
                       placeholder="Línea de investigación">
                @error('eiplanningbwsummary.linea_investigacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label font-weight-bold">{{ $list_comment_summary['enfasis_curriculares'] }}</label>
                <input type="text" class="form-control @error('eiplanningbwsummary.enfasis_curriculares') is-invalid @enderror" 
                       wire:model.defer="eiplanningbwsummary.enfasis_curriculares" 
                       placeholder="Énfasis curriculares">
                @error('eiplanningbwsummary.enfasis_curriculares')
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
            {{ $eiplanningbwsummary_id ? 'Actualizar' : 'Guardar' }} Resumen
        </button>
    </div>
</form>
