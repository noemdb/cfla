{{-- Sección de indicadores mejorada --}}
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);">
    <div class="card-body p-0">
        {{-- Contenedor de indicadores con React-like styling --}}
        <div id="education-indicators" class="p-4">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h3 class="mb-1 font-weight-bold" style="background: linear-gradient(135deg, #1f2937, #6b7280); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        Indicadores de Planificación
                    </h3>
                    <small class="text-muted">Resumen estadístico actualizado en tiempo real</small>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-clock fa-sm mr-1"></i>
                        <small>Actualizado: {{ $last_updated ?? now()->format('H:i') }}</small>
                    </div>
                    <div class=" mx-2">
                        @include('evaluacions.inicilas.partials.info')
                    </div>
                </div>

            </div>

            {{-- Indicadores principales --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden"
                         style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-2 font-weight-medium">Total de Registros</p>
                                    <div class="d-flex align-items-baseline">
                                        <h2 class="mb-0 font-weight-bold text-dark mr-2">{{ $education_stats['totalRecords'] }}</h2>
                                        <span class="badge badge-success badge-pill">+12%</span>
                                    </div>
                                </div>
                                <div class="p-3 rounded-circle shadow-lg" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                    <i class="fas fa-chart-bar text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="position-absolute w-100" style="bottom: 0; height: 4px; background: linear-gradient(90deg, #3b82f6, #2563eb);"></div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden"
                         style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-2 font-weight-medium">Proyectos Activos</p>
                                    <div class="d-flex align-items-baseline">
                                        <h2 class="mb-0 font-weight-bold text-dark mr-2">{{ $education_stats['activeProjects'] }}</h2>
                                        <span class="badge badge-info badge-pill">+5</span>
                                    </div>
                                </div>
                                <div class="p-3 rounded-circle shadow-lg" style="background: linear-gradient(135deg, #10b981, #059669);">
                                    <i class="fas fa-project-diagram text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="position-absolute w-100" style="bottom: 0; height: 4px; background: linear-gradient(90deg, #10b981, #059669);"></div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden"
                         style="background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%); transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-2 font-weight-medium">Evaluaciones Programadas</p>
                                    <div class="d-flex align-items-baseline">
                                        <h2 class="mb-0 font-weight-bold text-dark mr-2">{{ $education_stats['completedEvaluations'] }}</h2>
                                        <span class="badge badge-secondary badge-pill">67%</span>
                                    </div>
                                </div>
                                <div class="p-3 rounded-circle shadow-lg" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                    <i class="fas fa-chart-pie text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="position-absolute w-100" style="bottom: 0; height: 4px; background: linear-gradient(90deg, #8b5cf6, #7c3aed);"></div>
                    </div>
                </div>
            </div>

            {{-- Indicadores detallados --}}
            <div class="row">
                @php
                // Obtener el objeto peducativo si no está disponible directamente
                $peducativo = $peducativo ?? (
                    isset($eiplanningwks) && $eiplanningwks->isNotEmpty()
                        ? $eiplanningwks->first()->peducativo
                        : (isset($eiprojectks) && $eiprojectks->isNotEmpty()
                            ? $eiprojectks->first()->peducativo
                            : null)
                );

                $detailIndicators = [
                    [
                        'title' => 'Plan Semanal',
                        'value' => $education_stats['eiplanningwks'],
                        'icon' => 'fa-calendar-week',
                        'color' => '#0ea5e9',
                        'bg' => '#f0f9ff',
                        'max' => $peducativo->max_number_eiplanningwks ?? 50
                    ],
                    [
                        'title' => 'Plan Quincenal',
                        'value' => $education_stats['eiplanningbwks'],
                        'icon' => 'fa-book-open',
                        'color' => '#14b8a6',
                        'bg' => '#f0fdfa',
                        'max' => $peducativo->max_number_eiplanningbwks ?? 30
                    ],
                    [
                        'title' => 'Proyectos de Aula',
                        'value' => $education_stats['eiprojectks'],
                        'icon' => 'fa-bullseye',
                        'color' => '#6366f1',
                        'bg' => '#f0f0ff',
                        'max' => $peducativo->max_number_eiprojectks ?? 20
                    ],
                    [
                        'title' => 'Planes Especiales',
                        'value' => $education_stats['eispecialks'],
                        'icon' => 'fa-star',
                        'color' => '#f59e0b',
                        'bg' => '#fffbeb',
                        'max' => $peducativo->max_number_eispecialks ?? 15
                    ],
                    [
                        'title' => 'Plan de Evaluación',
                        'value' => $education_stats['eievaluationks'],
                        'icon' => 'fa-clipboard-check',
                        'color' => '#ef4444',
                        'bg' => '#fef2f2',
                        'max' => $peducativo->max_number_eievaluationks ?? 25
                    ],
                    [
                        'title' => 'Informes Finales',
                        'value' => $education_stats['eifinalks'],
                        'icon' => 'fa-file-alt',
                        'color' => '#8b5cf6',
                        'bg' => '#faf5ff',
                        'max' => $peducativo->max_number_eifinalks ?? 40
                    ],
                ];
                @endphp

                @foreach($detailIndicators as $indicator)
                @php
                $percentage = $indicator['max'] > 0 ? min(($indicator['value'] / $indicator['max']) * 100, 100) : 0;
                @endphp
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 text-center position-relative overflow-hidden hover-lift"
                         style="background-color: {{ $indicator['bg'] }}; transition: all 0.3s ease; cursor: pointer;"
                         onmouseover="this.style.transform='translateY(-5px) scale(1.02)'"
                         onmouseout="this.style.transform='translateY(0) scale(1)'">
                        <div class="card-body p-3">
                            <div class="d-inline-flex p-2 rounded-circle mb-3" style="background-color: {{ $indicator['color'] }}20;">
                                <i class="fas {{ $indicator['icon'] }} fa-lg" style="color: {{ $indicator['color'] }};"></i>
                            </div>

                            <h6 class="card-title mb-2 font-weight-semibold text-muted" style="font-size: 0.75rem; line-height: 1.2;">
                                {{ $indicator['title'] }}
                            </h6>

                            <div class="mb-2">
                                <h3 class="font-weight-bold mb-1" style="color: {{ $indicator['color'] }};">
                                    {{ $indicator['value'] }}
                                </h3>
                                <small class="text-muted">de {{ $indicator['max'] }}</small>
                            </div>

                            {{-- Progress bar --}}
                            <div class="progress mb-2" style="height: 6px; background-color: rgba(255,255,255,0.5);">
                                <div class="progress-bar"
                                     style="width: {{ $percentage }}%; background: linear-gradient(90deg, {{ $indicator['color'] }}, {{ $indicator['color'] }}cc); transition: width 1s ease-out;">
                                </div>
                            </div>

                            <small class="text-muted">
                                {{ number_format($percentage, 0) }}% del objetivo
                                @if($indicator['value'] >= $indicator['max'])
                                    <i class="fas fa-check-circle text-success ml-1" title="Objetivo alcanzado"></i>
                                @elseif($percentage >= 80)
                                    <i class="fas fa-exclamation-triangle text-warning ml-1" title="Cerca del objetivo"></i>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Información de configuración --}}
            @if($peducativo)
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Configuración de objetivos:</strong>
                        Los valores máximos están configurados según el programa educativo
                        <strong>"{{ $peducativo->name ?? 'Educación Inicial' }}"</strong>.
                        <small class="d-block mt-1">
                            Puede ajustar estos valores en la configuración del programa educativo.
                        </small>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- Footer --}}
            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center mr-4">
                        <i class="fas fa-trending-up text-success mr-1"></i>
                        <small class="text-muted">Tendencia positiva</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users text-primary mr-1"></i>
                        <small class="text-muted">Sistema activo</small>
                    </div>
                </div>
                <small class="text-muted">Educación Inicial • Gestión Escolar</small>
            </div>
        </div>
    </div>
</div>


@section('stylesheet')
@parent

<style>
.hover-lift {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.hover-lift:hover {
    transform: translateY(-5px) scale(1.02) !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

.progress-bar {
    transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#education-indicators .card {
    animation: fadeInUp 0.6s ease-out;
}

#education-indicators .card:nth-child(1) { animation-delay: 0.1s; }
#education-indicators .card:nth-child(2) { animation-delay: 0.2s; }
#education-indicators .card:nth-child(3) { animation-delay: 0.3s; }
</style>

@endsection
