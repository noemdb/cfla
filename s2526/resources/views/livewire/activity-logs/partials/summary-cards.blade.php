{{-- Tarjeta de Total de Actividades --}}
<div class="col-md-6 col-lg-3 mb-4">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total de Actividades</h6>
                    <h3 class="mb-0">{{ number_format($stats['totalActivities']) }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 rounded p-3">
                    <i class="fas fa-history fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tarjeta de Promedio Diario --}}
<div class="col-md-6 col-lg-3 mb-4">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Promedio Diario</h6>
                    <h3 class="mb-0">
                        @php
                            $days = Carbon\Carbon::parse($start_date)->diffInDays(Carbon\Carbon::parse($end_date)) + 1;
                            $avg = $days > 0 ? $stats['totalActivities'] / $days : 0;
                        @endphp
                        {{ number_format($avg, 1) }}
                    </h3>
                </div>
                <div class="bg-info bg-opacity-10 rounded p-3">
                    <i class="fas fa-chart-line fa-2x text-info"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tarjeta de URL más Accedida --}}
<div class="col-md-6 col-lg-3 mb-4">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">URL más Accedida</h6>
                    <h3 class="mb-0 text-truncate" style="max-width: 200px;" title="{{ $stats['mostAccessedUrl']->url ?? 'N/A' }}">
                        {{ $stats['mostAccessedUrl']->url ?? 'N/A' }}
                    </h3>
                    <small class="text-muted">
                        {{ number_format($stats['mostAccessedUrl']->count ?? 0) }} accesos
                    </small>
                </div>
                <div class="bg-success bg-opacity-10 rounded p-3">
                    <i class="fas fa-link fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tarjeta de Usuario más Activo --}}
<div class="col-md-6 col-lg-3 mb-4">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Usuario más Activo</h6>
                    <h3 class="mb-0 text-truncate" style="max-width: 200px;" title="{{ $stats['mostActiveUser']->username ?? 'N/A' }}">
                        {{ $stats['mostActiveUser']->username ?? 'N/A' }}
                    </h3>
                    <small class="text-muted">
                        {{ number_format($stats['mostActiveUser']->count ?? 0) }} actividades
                    </small>
                </div>
                <div class="bg-warning bg-opacity-10 rounded p-3">
                    <i class="fas fa-user fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>
</div>
