<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header alert-info text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-users"></i> Indicadores de Censos (Manifestaciones de interés)
                    @if($representant_ci)
                        <small class="ml-2">(Filtrado por CI: {{ $representant_ci }})</small>
                    @endif
                </h5>
            </div>
            <div class="card-body">

                {{-- Estadísticas Principales --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center border-primary h-100">
                            <div class="card-body py-3">
                                <h3 class="text-primary mb-1">{{ $catchment_indicators['totals']['total_catchments'] }}</h3>
                                <small class="text-muted">Total Censos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-success h-100">
                            <div class="card-body py-3">
                                <h3 class="text-success mb-1">{{ $catchment_indicators['totals']['with_interviews'] }}</h3>
                                <small class="text-muted">Con Entrevista</small>
                                <div class="small text-success font-weight-bold">
                                    {{ $catchment_indicators['percentages']['interview_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-warning h-100">
                            <div class="card-body py-3">
                                <h3 class="text-warning mb-1">{{ $catchment_indicators['totals']['without_interviews'] }}</h3>
                                <small class="text-muted">Sin Entrevista</small>
                                <div class="small text-warning font-weight-bold">
                                    {{ 100 - $catchment_indicators['percentages']['interview_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-info h-100">
                            <div class="card-body py-3">
                                <h3 class="text-info mb-1">{{ $catchment_indicators['totals']['foreign_students'] }}</h3>
                                <small class="text-muted">Extranjeros</small>
                                <div class="small text-info font-weight-bold">
                                    {{ $catchment_indicators['percentages']['foreign_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estadísticas Detalladas --}}
                <div class="row">
                    {{-- Por Grado --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-graduation-cap"></i> Por Grado/Año</h6>
                            </div>
                            <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                @if($catchment_indicators['by_grade']->count() > 0)
                                    @foreach($catchment_indicators['by_grade'] as $grade)
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small>
                                                {{ $grade->grado ? $grade->grado->name : 'Grado ' . $grade->grade }}
                                            </small>
                                            <span class="badge badge-primary">{{ $grade->total }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <small class="text-muted">No hay datos disponibles</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Por Género --}}
                    {{-- <div class="col-md-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-venus-mars"></i> Por Género</h6>
                            </div>
                            <div class="card-body p-2">
                                @if($catchment_indicators['by_gender']->count() > 0)
                                    @foreach($catchment_indicators['by_gender'] as $gender)
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small>
                                                <i class="fas fa-{{ $gender->gender == 'Masculino' ? 'mars text-primary' : 'venus text-danger' }}"></i>
                                                {{ $gender->gender }}
                                            </small>
                                            <span class="badge badge-{{ $gender->gender == 'Masculino' ? 'primary' : 'danger' }}">
                                                {{ $gender->total }}
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <small class="text-muted">No hay datos disponibles</small>
                                @endif
                            </div>
                        </div>
                    </div> --}}

                    {{-- Por Institución de Origen --}}
                    {{-- <div class="col-md-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-school"></i> Instituciones de Origen</h6>
                            </div>
                            <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                @if($catchment_indicators['by_origin']->count() > 0)
                                    @foreach($catchment_indicators['by_origin']->take(8) as $origin)
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small title="{{ $origin->origin }}">
                                                {{ Str::limit($origin->origin, 20) }}
                                            </small>
                                            <span class="badge badge-info">{{ $origin->total }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <small class="text-muted">No hay datos disponibles</small>
                                @endif
                            </div>
                        </div>
                    </div> --}}

                    {{-- Por Rangos de Edad --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-birthday-cake"></i> Rangos de Edad</h6>
                            </div>
                            <div class="card-body p-2">
                                @foreach($catchment_indicators['age_ranges'] as $range => $count)
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                        <small>{{ $range }} años</small>
                                        <span class="badge badge-secondary">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tendencia por Mes --}}
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-chart-line"></i> Tendencia - Últimos 6 Meses</h6>
                            </div>
                            <div class="card-body p-2">
                                @if($catchment_indicators['by_month']->count() > 0)
                                    @foreach($catchment_indicators['by_month'] as $month)
                                        @php
                                            $monthName = DateTime::createFromFormat('!m', $month->month)->format('M');
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small>{{ $monthName }} {{ $month->year }}</small>
                                            <span class="badge badge-primary">{{ $month->total }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <small class="text-muted">No hay datos de tendencia</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Días de Cita --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-calendar-day"></i> Días de Cita</h6>
                            </div>
                            <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                @if($catchment_indicators['by_appointment_day']->count() > 0)
                                    @foreach($catchment_indicators['by_appointment_day'] as $appointment)
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small>{{ $appointment->day_appointment ? \Carbon\Carbon::parse($appointment->day_appointment)->format('d/m/Y') : 'Sin fecha' }}</small>
                                            <span class="badge badge-info">{{ $appointment->total }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <small class="text-muted">No hay citas programadas</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Enlaces rápidos --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>Enlaces rápidos:</strong></span>
                                <div>
                                    <a href="{{ route('bienestars.matriculations.catchments.index') }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-bars"></i> Ver todas las manifestaciones de interés registrados
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
