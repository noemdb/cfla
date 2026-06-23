<div>

    <div class="fw-bold text-muted border-top my-1 py-1">Planificación, Acitivades</div>

    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item border rounded">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    <span class="fw-bold text-muted">Registrar</span>
                    
                </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse {{ ($modeCreate) ? 'show' : null}}" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body p-2">
                    {{-- <livewire:movile.profesor.learning.lesson-create-component /> --}}
                    <livewire:movile.profesor.activity.create-component />
                </div>
            </div>
        </div>
        <div class="accordion-item border rounded">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    <span class="fw-bold text-muted">Listado</span>
                </button>
            </h2>
            <div id="flush-collapseTwo" class="accordion-collapse collapse {{ ($modeIndex) ? 'show' : null}}" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body p-2">
                    {{-- <livewire:movile.profesor.learning.lesson-list-component /> --}}
                    <livewire:movile.profesor.activity.list-component />
                </div>
            </div>
        </div>
    </div>

</div>


