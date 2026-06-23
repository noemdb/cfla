<div>
    {{-- Contexto informativo de la pevaluacion --}}
    @if($act_selected_pevaluacion ?? false)
    <div class="alert alert-info p-2 mb-3 small">
        <strong><i class="fas fa-book-open mr-1"></i> Plan de Evaluación:</strong>
        {{ $act_selected_pevaluacion->pensum->asignatura->name ?? '' }}
        <span class="font-weight-bold">
            [{{ $act_selected_pevaluacion->pensum->grado->name ?? '' }} {{ $act_selected_pevaluacion->seccion->name ?? '' }}]
        </span>
        <span class="badge badge-info ml-1">{{ $act_selected_pevaluacion->lapso->name ?? '' }}</span>
    </div>
    @endif

    {{-- Fechas --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="small font-weight-bold text-muted mb-1">Fecha Inicial <span class="text-danger">*</span></label>
            <input type="date" wire:model.defer="act_finicial" class="form-control form-control-sm">
            @error('act_finicial') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="small font-weight-bold text-muted mb-1">Fecha Final <span class="text-danger">*</span></label>
            <input type="date" wire:model.defer="act_ffinal" class="form-control form-control-sm">
            @error('act_ffinal') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- Nombre (opcional) --}}
    <div class="mb-3">
        <label class="small font-weight-bold text-muted mb-1">Nombre de la Actividad</label>
        <input type="text" wire:model.defer="act_name" class="form-control form-control-sm" placeholder="Nombre opcional">
        @error('act_name') <span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    {{-- Tema Generador --}}
    <div class="mb-3">
        <label class="small font-weight-bold text-muted mb-1">Tema Generador y Énfasis <span class="text-danger">*</span></label>
        <textarea wire:model.defer="act_topic" class="form-control form-control-sm" rows="2" placeholder="Tema generador y énfasis"></textarea>
        @error('act_topic') <span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    {{-- Tejido Temático --}}
    <div class="mb-3">
        <label class="small font-weight-bold text-muted mb-1">Tejido Temático / Tema Indispensable</label>
        <textarea wire:model.defer="act_thematic" class="form-control form-control-sm" rows="2" placeholder="Tejido temático"></textarea>
        @error('act_thematic') <span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    {{-- Referentes --}}
    <div class="mb-3">
        <label class="small font-weight-bold text-muted mb-1">Referentes Teórico Prácticos y Éticos</label>
        <textarea wire:model.defer="act_references" class="form-control form-control-sm" rows="2" placeholder="Referentes"></textarea>
        @error('act_references') <span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    {{-- Enseñanza y Aprendizaje --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="small font-weight-bold text-muted mb-1">Enseñanza / Actividad Globalizada</label>
            <textarea wire:model.defer="act_teaching" class="form-control form-control-sm" rows="3" placeholder="Enseñanza"></textarea>
            @error('act_teaching') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="small font-weight-bold text-muted mb-1">Aprendizaje</label>
            <textarea wire:model.defer="act_learning" class="form-control form-control-sm" rows="3" placeholder="Aprendizaje"></textarea>
            @error('act_learning') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- Actividad Evaluativa --}}
    <div class="mb-3">
        <label class="small font-weight-bold text-muted mb-1">Actividad Evaluativa</label>
        <textarea wire:model.defer="act_description" class="form-control form-control-sm" rows="2" placeholder="Descripción de la actividad evaluativa"></textarea>
        @error('act_description') <span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    {{-- Indicadores de Logro y Ponderación --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="small font-weight-bold text-muted mb-1">Indicadores de Logro</label>
            <textarea wire:model.defer="act_achievement" class="form-control form-control-sm" rows="2" placeholder="Indicadores de logro"></textarea>
            @error('act_achievement') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-3 mb-3">
            <label class="small font-weight-bold text-muted mb-1">Ponderación</label>
            <input type="number" wire:model.defer="act_weighting" class="form-control form-control-sm" min="0" max="100" placeholder="0-100">
            @error('act_weighting') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-end">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model.defer="act_status" class="custom-control-input" id="actStatusSwitch">
                <label class="custom-control-label font-weight-bold" for="actStatusSwitch">Aprobada</label>
            </div>
        </div>
    </div>

    {{-- Observaciones --}}
    <div class="mb-3">
        <label class="small font-weight-bold text-muted mb-1">ODS / Sistematización</label>
        <textarea wire:model.defer="act_observations" class="form-control form-control-sm" rows="2" placeholder="Observaciones / ODS"></textarea>
        @error('act_observations') <span class="text-danger small">{{ $message }}</span> @enderror
    </div>
</div>
