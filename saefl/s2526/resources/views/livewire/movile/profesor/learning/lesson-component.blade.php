<div>

    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item border rounded">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    Registrar Lecciones
                </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse {{ ($modeCreate) ? 'show' : null}}" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body p-2">
                    <livewire:movile.profesor.learning.lesson-create-component />
                </div>
            </div>
        </div>
        <div class="accordion-item border rounded">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    Listado
                </button>
            </h2>
            <div id="flush-collapseTwo" class="accordion-collapse collapse {{ ($modeIndex) ? 'show' : null}}" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body p-2">
                    <livewire:movile.profesor.learning.lesson-list-component />
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button
                    class="accordion-button"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseOne"
                    aria-expanded="true"
                    aria-controls="collapseOne"
                >
                    Accordion Item #1
                </button>
            </h2>
            <div
                id="collapseOne"
                class="accordion-collapse collapse show"
                aria-labelledby="headingOne"
                data-bs-parent="#accordionExample"
            >
                <div class="accordion-body">
                    This is the first item's accordion body.
                </div>
            </div>
        </div>
        
    </div> --}}

</div>


