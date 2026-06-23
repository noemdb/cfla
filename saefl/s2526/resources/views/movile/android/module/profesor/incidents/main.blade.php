<div>

    <i class="bi bi-file-text" style="font-size: 2rem"></i>
    <h5 class="card-title text-dark">Incidencias</h5>

    <div class="p-1">
        <div class="accordion" id="accordionIncident">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThreeIncident" aria-expanded="false" aria-controls="collapseThreeIncident">
                    Registrar
                    </button>
                </h2>
                <div id="collapseThreeIncident" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionIncident">
                    <div class="accordion-body p-2">
                        <livewire:movile.profesor.incident.create-component />
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo-Incident" aria-expanded="false" aria-controls="collapseTwo-Incident">
                        Incidencias Registradas
                    </button>
                </h2>
                <div id="collapseTwo-Incident" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionIncident">
                    <div class="accordion-body p-2">
                        <livewire:movile.profesor.incident.index-component />
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree-Incident" aria-expanded="false" aria-controls="collapseTheer-Incident">
                        Itinerario de reuniones
                    </button>
                </h2>
                <div id="collapseThree-Incident" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionIncident">
                    <div class="accordion-body p-2">
                        <livewire:movile.profesor.incident.itinerary-component />
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour-Incident" aria-expanded="false" aria-controls="collapseTheer-Incident">
                        Línea de Tiempo
                    </button>
                </h2>
                <div id="collapseFour-Incident" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionIncident">
                    <div class="accordion-body p-2">
                        <livewire:movile.profesor.incident.tline-component />
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

