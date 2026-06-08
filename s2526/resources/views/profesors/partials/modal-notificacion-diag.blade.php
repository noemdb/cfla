@if($esProfesorGuia && $tieneReportesDiagnosticos)
<!-- Modal de Notificación de Diagnóstico -->
<div class="modal fade" id="modalNotificacionDiagnostico" tabindex="-1" role="dialog" 
     aria-labelledby="modalNotificacionDiagnosticoLabel" 
     aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-gradient-primary text-white rounded-top">
                <h5 class="modal-title font-weight-bold" id="modalNotificacionDiagnosticoLabel">
                    <i class="fas fa-chart-line mr-2"></i> ¡INFORME DE DIAGNÓSTICO DISPONIBLE!
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body py-4">
                
                <!-- Mensaje Principal -->
                <div class="alert alert-primary border-left-primary border-left-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading font-weight-bold mb-2">Estimado Profesor Tutor</h5>
                            <p class="mb-0">
                                Se han generado <strong>informes de diagnóstico académico</strong> para las secciones que usted guía. 
                                Estos reportes le permitirán analizar el desempeño de sus estudiantes y tomar decisiones pedagógicas informadas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Información del Último Reporte -->
                @if($ultimoReporte && $ultimoReporte->section && $ultimoReporte->section->grado)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-calendar-check mr-2"></i> Último Informe Generado
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="text-muted small">Diagnóstico:</span>
                                    <div class="font-weight-bold text-dark">
                                        {{ $ultimoReporte->diagMain->name ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small">Sección:</span>
                                    <div class="font-weight-bold text-dark">
                                        {{ $ultimoReporte->section->grado->name ?? 'Grado' }} - Sección {{ $ultimoReporte->section->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="text-muted small">Generado:</span>
                                    <div class="font-weight-bold text-dark">
                                        {{ $ultimoReporte->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                                @if(isset($ultimoReporte->global_precision_avg))
                                <div class="mb-3">
                                    <span class="text-muted small">Precisión Global:</span>
                                    <div class="font-weight-bold text-{{ $ultimoReporte->global_precision_avg >= 75 ? 'success' : ($ultimoReporte->global_precision_avg >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($ultimoReporte->global_precision_avg, 1) }}%
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Secciones con Reportes Disponibles -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-users mr-2"></i> Secciones con Reportes Disponibles
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                        	@php
								$seccion_guias = $profesor->seccion_guias;		
                                //dd($seccion_guias);
                        	@endphp
                            @foreach($seccion_guias as $seccion)
                            @php
                                $reportesCount = App\Models\app\Instrument\SectionDiagnosticReport::where('section_id', $seccion->id)->count();
                            @endphp
                            @if($reportesCount > 0)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 border rounded bg-light hover-shadow">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-folder-open fa-2x text-primary mr-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="font-weight-bold mb-1">
                                            {{ $seccion->grado->name ?? 'Grado' }} - Sección {{ $seccion->name }}
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-file-alt mr-1"></i> {{ $reportesCount }} informe(s) disponible(s)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Beneficios del Informe -->
                <div class="alert alert-success border-left-success border-left-3 mt-4">
                    <h6 class="font-weight-bold mb-3">
                        <i class="fas fa-star mr-2"></i> ¿Qué encontrará en los informes?
                    </h6>
                    <ul class="mb-0">
                        <li>Análisis de fortalezas y debilidades por área</li>
                        <li>Distribución del desempeño estudiantil</li>
                        <li>Recomendaciones pedagógicas específicas</li>
                        <li>Comparativas con el currículo esperado</li>
                        <li>Información para planificación diferenciada</li>
                    </ul>
                </div>

            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer bg-light rounded-bottom">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
                
                <!-- Botón para ir a la sección especial -->
                <a href="{{ route('profesors.profesor_guias.index') }}" class="btn btn-primary">
                    <i class="fas fa-external-link-alt mr-1"></i> Ir a Informes de Diagnóstico
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Script para manejar el modal -->
@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Mostrar el modal automáticamente cuando se carga la página
    $('#modalNotificacionDiagnostico').modal('show');
    
    // Manejar el botón "No mostrar más"
    $('#btnNoMostrarMas').click(function() {
        // Guardar en localStorage para no mostrar el modal nuevamente
        localStorage.setItem('noMostrarNotificacionDiagnostico', 'true');
        $('#modalNotificacionDiagnostico').modal('hide');
        
        // Mostrar mensaje de confirmación
        Swal.fire({
            icon: 'info',
            title: 'Notificación oculta',
            text: 'Esta notificación no se mostrará nuevamente. Podrá acceder a los informes desde la sección especial del menú.',
            timer: 3000,
            showConfirmButton: false
        });
    });
    
    // Opcional: Verificar si el usuario ya decidió no ver el modal
    if (localStorage.getItem('noMostrarNotificacionDiagnostico') === 'true') {
        $('#modalNotificacionDiagnostico').modal('hide');
    }
    
    // Limpiar localStorage cuando cierren sesión (opcional)
    $(document).on('click', 'a[href*="logout"]', function() {
        localStorage.removeItem('noMostrarNotificacionDiagnostico');
    });
});
</script>
<style>
.hover-shadow {
    transition: all 0.3s ease;
    cursor: pointer;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
.border-left-success {
    border-left: 4px solid #28a745 !important;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}
</style>
@endsection
@endif