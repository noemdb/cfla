<!-- Tarjeta Informativa para Profesor Guía -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0 font-weight-bold">
            <i class="fas fa-user-tie mr-2"></i> Profesor Guía
        </h6>
    </div>
    <div class="card-body">
        <div class="text-center mb-3">
            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
            <h6 class="font-weight-bold text-dark">Informes de Diagnóstico</h6>
            <p class="small text-muted">Acceda a los reportes de desempeño académico de sus secciones</p>
        </div>
        
        <div class="list-group list-group-flush">
            @foreach($seccionesGuia as $seccion)
            @php
                $reportesCount = App\Models\app\Instrument\SectionDiagnosticReport::where('section_id', $seccion->id)->count();
            @endphp
            <a href="{{ route('profesors.profesor_guias.index') }}" 
               class="list-group-item list-group-item-action border-0 py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-users text-primary mr-2"></i>
                        <span class="font-weight-bold">{{ $seccion->grado->name ?? 'Grado' }}-{{ $seccion->name }}</span>
                    </div>
                    @if($reportesCount > 0)
                    <span class="badge badge-pill badge-success">{{ $reportesCount }}</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        
        <div class="mt-3 text-center">
            <a href="{{ route('profesors.profesor_guias.index') }}" class="btn btn-info btn-sm btn-block">
                <i class="fas fa-external-link-alt mr-1"></i> Ver Todos los Informes
            </a>
        </div>
    </div>
</div>