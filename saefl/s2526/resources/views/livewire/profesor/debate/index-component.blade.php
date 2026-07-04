<div>

    <div wire:target="generateAiDebate" wire:loading.class="d-flex flex-column"
        class="position-fixed w-100 h-100 bg-dark align-items-center justify-content-center opacity-50"
        style="top: 0; left: 0; z-index: 1050; display: none;">
        <div class="spinner-border text-success" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <h2 class="text-white font-weight-bold">Generando IA...</h2>
    </div>

    <div wire:target="aiGenerateCompetition" wire:loading.class="d-flex flex-column"
        class="position-fixed w-100 h-100 bg-dark align-items-center justify-content-center opacity-50"
        style="top: 0; left: 0; z-index: 1050; display: none;">
        <div class="spinner-border text-success" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <h2 class="text-white font-weight-bold">Generando IA...</h2>
    </div>

    @includeWhen($modeCreator, 'livewire.profesor.debate.overlay.create')
    @includeWhen($modeCreatorGeminiCompetition, 'livewire.profesor.debate.overlay.geminiCreate')
    @includeWhen($modeCreatorGeminiDebate, 'livewire.profesor.debate.overlay.createDebateGemini', [
        'competition_id' => $competition_id,
    ])
    @includeWhen($modeCreatorGroup, 'livewire.profesor.debate.overlay.createGroup')

    <div class="font-weight-normal h5">Listado de las competiciones registradas</div>

    <div class="input-group mb-2">

        {!! Form::select('competition_id', $list_competition, old('competition_id'), [
            'wire:model' => 'competition_id',
            'class' => 'custom-select',
            'style' => 'height:2.5rem',
            'placeholder' => 'Selecciones',
        ]) !!}
        <div class="input-group-append">
            <button type="button" class="btn btn-primary" wire:click="setCreate()" title="Registrar nueva competición">
                <i class="{{ $icon_menus['nuevo'] ?? '' }}" aria-hidden="true"></i>
            </button>
            {{-- <button type="button" class="btn btn-dark" wire:click="gemiCreateCompetition()" title="Registrar nueva competición con IA"> --}}
            <button type="button" class="btn btn-dark" wire:click="aiCreateCompetition()"
                title="Registrar nueva competición con IA">
                <i class="{{ $icon_menus['info'] ?? '' }}" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    @includeWhen($modeIndex, 'livewire.profesor.debate.table.index')

</div>
