<div class="container-fluid p-0">
    <!-- Encabezado del Diagnóstico -->
    <div class="card border-0 mb-4 shadow-sm">
        <div class="card-body bg-primary text-white rounded-top">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="font-weight-bold mb-1">
                        <i class="fas fa-chart-line mr-2"></i> INFORME DIAGNÓSTICO
                    </h4>
                    <h5 class="mb-0 opacity-90">
                        {{ $diagMain->name }}
                    </h5>
                </div>
                <div class="col-md-4 text-md-right">
                    <span class="badge badge-light text-dark px-3 py-2">
                        <i class="fas fa-id-badge mr-1"></i> ID: {{ $diagMain->id }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Información de la sección -->
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-graduation-cap text-primary mr-2"></i>
                        <div>
                            <div class="font-weight-bold text-dark">
                                {{ $seccion->grado->name ?? 'Grado' }} - Sección {{ $seccion->name }}
                            </div>
                            <div class="small text-muted">
                                {{ $seccion->grado->pestudio->name ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-right">
                    <div class="d-inline-block text-center">
                        <div class="small text-muted">Estudiantes</div>
                        <div class="h4 font-weight-bold text-primary mb-0">
                            {{ $seccion->estudiants_in->count() ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($sectionReport)
        <!-- Mostrar el reporte específico -->
        @include('profesors.profesor_guias.modal.partials.section-report', ['report' => $sectionReport])
    @else
        <!-- Si no hay reporte para esta combinación -->
        <div class="alert alert-info text-center py-5 my-4">
            <i class="fas fa-info-circle fa-3x mb-4 text-info"></i>
            <h4 class="font-weight-bold mb-3">No hay datos disponibles</h4>
            <p class="mb-4">
                No se encontró un reporte de diagnóstico para la sección 
                <strong>{{ $seccion->grado->name ?? 'Grado' }} - Sección {{ $seccion->name }}</strong>
                en el diagnóstico <strong>{{ $diagMain->name }}</strong>.
            </p>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-times mr-1"></i> Cerrar
            </button>
        </div>
    @endif
</div>

<!-- Selector de otros diagnósticos (si hay disponibles) -->
@if($allDiagMains->count() > 1)
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
        <h6 class="mb-0 font-weight-bold text-dark">
            <i class="fas fa-exchange-alt mr-2"></i> Ver otros diagnósticos
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($allDiagMains as $otherDiag)
                @if($otherDiag->id != $diagMain->id)
                <div class="col-md-4 mb-3">
                    <a href="{{ route('profesors.profesor_guias.diag.show', [
                        'diagMain' => $otherDiag->id,
                        'seccion' => $seccion->id
                    ]) }}"
                       class="card border text-decoration-none h-100 hover-shadow">
                        <div class="card-body text-center">
                            <h6 class="font-weight-bold text-dark mb-2">
                                {{ $otherDiag->name }}
                            </h6>
                            <div class="small text-muted mb-3">
                                ID: {{ $otherDiag->id }}
                            </div>
                            <span class="badge badge-info">
                                Ver reporte
                            </span>
                        </div>
                    </a>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Manejar clic en enlaces de otros diagnósticos
    $(document).on('click', '.card a[href*="profesor-guias/diagnostico"]', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        
        // Mostrar loader
        $('#modalDiagBody').html(
            '<div class="text-center py-5">' +
            '   <div class="spinner-border text-primary" role="status">' +
            '       <span class="sr-only">Cargando...</span>' +
            '   </div>' +
            '   <p class="mt-2 text-muted">Cargando nuevo diagnóstico...</p>' +
            '</div>'
        );
        
        // Cargar nuevo contenido
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $('#modalDiagBody').html(response);
            },
            error: function() {
                $('#modalDiagBody').html(
                    '<div class="alert alert-danger text-center py-5">' +
                    '   <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>' +
                    '   <h5 class="font-weight-bold">Error al cargar el diagnóstico</h5>' +
                    '   <button class="btn btn-secondary mt-3" onclick="$(\'#modalDiag\').modal(\'hide\')">Cerrar</button>' +
                    '</div>'
                );
            }
        });
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
.bg-success-soft {
    background-color: rgba(25, 135, 84, 0.1);
}
.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.1);
}
.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.1);
}
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
.border-left-success {
    border-left: 4px solid #28a745 !important;
}
.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}
</style>
@endsection