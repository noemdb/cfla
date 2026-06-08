@if ($showSectionReportModal && $selectedSectionReport)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-layer-group mr-2"></i> REPORTE DIAGNÓSTICO DE SECCIÓN
                    </h5>
                    <button type="button" class="close text-white" wire:click="closeSectionReportModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4 bg-light" style="max-height: 85vh; overflow-y: auto;">
                    <!-- Cabecera Informativa -->
                    <div class="card shadow-sm mb-4 border-0">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="font-weight-bold text-dark mb-1">
                                        {{ $selectedSectionReport->section->grado->name ?? 'Grado' }} - Sección
                                        {{ $selectedSectionReport->section->name }}
                                    </h4>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-calendar-alt mr-1"></i> Ciclo:
                                        {{ $selectedSectionReport->diagnostic_id }}
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-users mr-1"></i> Estudiantes Procesados:
                                        {{ $selectedSectionReport->students_count }}
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-right text-center mt-3 mt-md-0">
                                    <div class="p-2 border rounded bg-white shadow-sm d-inline-block">
                                        <div class="small text-uppercase text-muted font-weight-bold">Precisión Global
                                        </div>
                                        <div
                                            class="h3 font-weight-bold mb-0 {{ $selectedSectionReport->global_precision_avg >= 75 ? 'text-success' : ($selectedSectionReport->global_precision_avg >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ number_format($selectedSectionReport->global_precision_avg, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Resumen Global -->
                        <div class="col-lg-7">
                            <div class="card shadow-sm mb-4 border-0 h-100">
                                <div class="card-header bg-white font-weight-bold text-uppercase small text-primary">
                                    <i class="fas fa-chart-line mr-2"></i> Síntesis Global del Grupo
                                </div>
                                <div class="card-body">
                                    <div class="p-3 bg-light rounded italic text-dark mb-4">
                                        "{!! $selectedSectionReport->globalResult->global_summary ?? 'Sin resumen global disponible.' !!}"
                                    </div>

                                    <h6 class="font-weight-bold small text-uppercase text-muted mb-3">Distribución por
                                        Niveles</h6>
                                    @php
                                        $dist = $selectedSectionReport->globalResult->precision_distribution ?? [
                                            'HIGH' => 0,
                                            'MEDIUM' => 0,
                                            'LOW' => 0,
                                        ];
                                        $total = array_sum($dist) ?: 1;
                                    @endphp

                                    <div class="mb-4">
                                        <div class="progress shadow-sm mb-2"
                                            style="height: 25px; border-radius: 12px; overflow: hidden;">
                                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                                role="progressbar" style="width: {{ ($dist['HIGH'] / $total) * 100 }}%"
                                                title="ALTO: {{ $dist['HIGH'] }} estudiantes">
                                                {{ $dist['HIGH'] > 0 ? "ALTO ({$dist['HIGH']})" : '' }}
                                            </div>
                                            <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated"
                                                role="progressbar"
                                                style="width: {{ ($dist['MEDIUM'] / $total) * 100 }}%"
                                                title="MEDIO: {{ $dist['MEDIUM'] }} estudiantes">
                                                {{ $dist['MEDIUM'] > 0 ? "MEDIO ({$dist['MEDIUM']})" : '' }}
                                            </div>
                                            <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated"
                                                role="progressbar" style="width: {{ ($dist['LOW'] / $total) * 100 }}%"
                                                title="BAJO: {{ $dist['LOW'] }} estudiantes">
                                                {{ $dist['LOW'] > 0 ? "BAJO ({$dist['LOW']})" : '' }}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted px-1">
                                            <span><i class="fas fa-circle text-success mr-1"></i> Desempeño Alto</span>
                                            <span><i class="fas fa-circle text-warning mr-1"></i> Desempeño Medio</span>
                                            <span><i class="fas fa-circle text-danger mr-1"></i> Requiere Apoyo</span>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <div class="p-3 border rounded text-center bg-white shadow-xs">
                                                <i class="fas fa-brain text-info fa-2x mb-2"></i>
                                                <div class="small text-muted text-uppercase font-weight-bold">
                                                    Pensamiento Predominante</div>
                                                <div class="font-weight-bold text-dark">
                                                    {{ $selectedSectionReport->profile->dominant_processing_style ?? 'No determinado' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 border rounded text-center bg-white shadow-xs">
                                                <i class="fas fa-lightbulb text-warning fa-2x mb-2"></i>
                                                <div class="small text-muted text-uppercase font-weight-bold">
                                                    Aprendizaje Predominante</div>
                                                <div class="font-weight-bold text-dark">
                                                    {{ $selectedSectionReport->profile->dominant_learning_style ?? 'No determinado' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fortalezas y Necesidades -->
                        <div class="col-lg-5">
                            <div class="card shadow-sm mb-4 border-0 h-100">
                                <div class="card-header bg-white font-weight-bold text-uppercase small text-info">
                                    <i class="fas fa-user-check mr-2"></i> Perfil Pedagógico Grupal
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="font-weight-bold text-success small text-uppercase"><i
                                                class="fas fa-plus-circle mr-1"></i> Fortalezas del Grupo</label>
                                        <div class="p-2 border-left border-success bg-light rounded small">
                                            {{ is_array($selectedSectionReport->profile->strengths) ? implode(', ', $selectedSectionReport->profile->strengths) : $selectedSectionReport->profile->strengths ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="font-weight-bold text-danger small text-uppercase"><i
                                                class="fas fa-exclamation-circle mr-1"></i> Necesidades de Apoyo</label>
                                        <div class="p-2 border-left border-danger bg-light rounded small">
                                            {{ is_array($selectedSectionReport->profile->needs) ? implode(', ', $selectedSectionReport->profile->needs) : $selectedSectionReport->profile->needs ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="font-weight-bold text-primary small text-uppercase"><i
                                                class="fas fa-info-circle mr-1"></i> Resumen Cognitivo</label>
                                        <p class="small text-muted text-justify">
                                            {{ $selectedSectionReport->profile->cognitive_summary ?? 'Sin análisis cognitivo disponible.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desglose por Áreas -->
                    <div class="card shadow-sm mb-4 border-0 mt-4">
                        <div class="card-header bg-white font-weight-bold text-uppercase small text-dark border-bottom">
                            <i class="fas fa-book mr-2"></i> 4. Rendimiento por Área de Formación
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light small font-weight-bold">
                                        <tr>
                                            <th class="px-4 py-3">Área de Formación</th>
                                            <th class="py-3 text-center">Precisión Promedio</th>
                                            <th class="py-3">Debilidades Detectadas</th>
                                            <th class="py-3">Fortalezas Consolidadas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedSectionReport->areaResults as $area)
                                            <tr>
                                                <td class="px-4 font-weight-bold">{{ $area->area_name }}</td>
                                                <td class="text-center" style="width: 200px;">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <span
                                                            class="mr-2 font-weight-bold">{{ number_format($area->precision_avg, 1) }}%</span>
                                                        <div class="progress flex-grow-1"
                                                            style="height: 6px; width: 80px;">
                                                            <div class="progress-bar {{ $area->precision_avg >= 75 ? 'bg-success' : ($area->precision_avg >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                                role="progressbar"
                                                                style="width: {{ $area->precision_avg }}%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="small" style="max-width: 300px;">
                                                    @php $weaknesses = $area->insights->where('type', 'weakness')->take(3); @endphp
                                                    @if ($weaknesses->count() > 0)
                                                        <div class="d-flex flex-column gap-1">
                                                            @foreach ($weaknesses as $insight)
                                                                <div class="p-1 px-2 mb-1 rounded bg-danger-soft text-danger border-left border-danger"
                                                                    style="background-color: #fff5f5;">
                                                                    <i class="fas fa-times-circle mr-1 small"></i>
                                                                    {{ $insight->description }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted italic">Sin debilidades
                                                            críticas.</span>
                                                    @endif
                                                </td>
                                                <td class="small" style="max-width: 300px;">
                                                    <div class="d-flex flex-column gap-1">
                                                        @php $strengths = $area->insights->where('type', 'strength')->take(3); @endphp
                                                        @if ($strengths->count() > 0)
                                                            @foreach ($strengths as $insight)
                                                                <div class="p-1 px-2 mb-1 rounded bg-success-soft text-success border-left border-success"
                                                                    style="background-color: #f0fff4;">
                                                                    <i class="fas fa-check-circle mr-1 small"></i>
                                                                    {{ $insight->description }}
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted italic">Sin fortalezas
                                                                registradas.</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recomendaciones y Contraste -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm mb-4 border-0">
                                <div class="card-header bg-white font-weight-bold text-uppercase small text-dark">
                                    <i class="fas fa-bullseye mr-2"></i> Orientaciones por Actor
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush small">
                                        @foreach ($selectedSectionReport->recommendations->groupBy('type') as $actor => $recs)
                                            <div class="list-group-item bg-light font-weight-bold text-uppercase py-2">
                                                <i class="fas fa-user-tag mr-2 text-primary"></i> Dirigido a:
                                                {{ $actor }}
                                            </div>
                                            @foreach ($recs as $rec)
                                                <div class="list-group-item border-0 pl-4 py-2">
                                                    <i class="fas fa-chevron-right text-muted mr-2"></i>
                                                    {{ $rec->recommendation }}
                                                    @if ($rec->priority == 'HIGH')
                                                        <span
                                                            class="badge badge-danger-soft text-danger ml-1">Alta</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm mb-4 border-0">
                                <div class="card-header bg-white font-weight-bold text-uppercase small text-dark">
                                    <i class="fas fa-balance-scale mr-2"></i> Contrastes y Hallazgos Críticos
                                </div>
                                <div class="card-body">
                                    <p class="small text-muted mb-3 text-justify">
                                        {{ $selectedSectionReport->contrast->gaps ?? 'Análisis de brechas no generado.' }}
                                    </p>
                                    @if ($selectedSectionReport->contrast && $selectedSectionReport->contrast->critical_subjects)
                                        <label class="font-weight-bold text-danger small text-uppercase">Grave Atención
                                            en:</label>
                                        <div class="d-flex flex-wrap">
                                            @foreach ($selectedSectionReport->contrast->critical_subjects as $subj)
                                                <span
                                                    class="badge badge-danger px-2 py-1 mr-1 mb-1">{{ $subj }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary shadow-sm"
                        wire:click="closeSectionReportModal">Cerrar</button>
                    <button type="button" class="btn btn-primary shadow-sm" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i> Imprimir Reporte
                    </button>
                </div> --}}

            </div>
        </div>
    </div>
@endif
