<!-- Modal para Móvil -->
<div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1060;">
    <div class="modal-dialog modal-dialog-scrollable" style="max-height: 90vh; margin: 5vh auto;">
        <div class="modal-content" style="max-height: 90vh;">
            <!-- Header -->
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Guía Rápida
                </h6>
                <button type="button" class="btn-close btn-close-white" wire:click="toggleGuiaModal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-3" style="overflow-y: auto;">
                <!-- Contenido simplificado para móvil -->
                <div class="card mb-3 border-0 bg-light">
                    <div class="card-body p-3">
                        <h6 class="card-title"><i class="fas fa-filter me-2 text-primary"></i>Filtros Rápidos</h6>
                        <ul class="small mb-0">
                            <li><strong>Grado:</strong> Filtro principal obligatorio</li>
                            <li><strong>Más Filtros:</strong> Usa el acordeón para opciones avanzadas</li>
                            <li><strong>Estado:</strong> Pendiente (!) o Ejecutada (✓)</li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-3 border-0 bg-light">
                    <div class="card-body p-3">
                        <h6 class="card-title"><i class="fas fa-table me-2 text-primary"></i>Información en Tabla</h6>
                        <ul class="small mb-0">
                            <li><strong>Rojo:</strong> Sin notas registradas</li>
                            <li><strong>Verde:</strong> Evaluación ejecutada</li>
                            <li><strong>Amarillo:</strong> Evaluación pendiente</li>
                            <li><strong>Notas:</strong> Cantidad de calificaciones</li>
                            <li><strong>Prom:</strong> Promedio de calificaciones</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 bg-light">
                    <div class="card-body p-3">
                        <h6 class="card-title"><i class="fas fa-cogs me-2 text-primary"></i>Acciones</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <button class="btn btn-warning btn-sm mb-2" disabled>
                                    <i class="fas fa-clock"></i>
                                </button>
                                <div class="small">Pendiente</div>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-success btn-sm mb-2" disabled>
                                    <i class="fas fa-check"></i>
                                </button>
                                <div class="small">Ejecutada</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" wire:click="toggleGuiaModal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Backdrop -->
<div class="modal-backdrop fade show" wire:click="toggleGuiaModal" style="z-index: 1050;"></div>

<script>
document.addEventListener('livewire:load', function() {
    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && @this.showGuiaModal) {
            @this.toggleGuiaModal();
        }
    });

    // Prevenir scroll del body
    if (@this.showGuiaModal) {
        document.body.style.overflow = 'hidden';
    }

    Livewire.hook('message.processed', (message, component) => {
        if (component.serverMemo.data.showGuiaModal) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    });
});
</script>