<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header alert-success text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar"></i> Indicadores de Entrevistas
                    @if($representant_ci)
                        <small class="ml-2">(Filtrado por CI: {{ $representant_ci }})</small>
                    @endif
                </h5>
            </div>
            <div class="card-body">

                {{-- Estadísticas Principales --}}
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card text-center border-primary h-100">
                            <div class="card-body py-3">
                                <h3 class="text-primary mb-1">{{ $interview_indicators['totals']['total_interviews'] }}</h3>
                                <small class="text-muted">Total Entrevistas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center border-success h-100">
                            <div class="card-body py-3">
                                <h3 class="text-success mb-1">{{ $interview_indicators['totals']['accepted'] }}</h3>
                                <small class="text-muted">Aceptados</small>
                                <div class="small text-success font-weight-bold">
                                    {{ $interview_indicators['percentages']['acceptance_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center border-warning h-100">
                            <div class="card-body py-3">
                                <h3 class="text-warning mb-1">{{ $interview_indicators['totals']['standby'] }}</h3>
                                <small class="text-muted">En Espera</small>
                                <div class="small text-warning font-weight-bold">
                                    {{ $interview_indicators['percentages']['standby_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center border-info h-100">
                            <div class="card-body py-3">
                                <h3 class="text-info mb-1">{{ $interview_indicators['totals']['notified'] }}</h3>
                                <small class="text-muted">Notificados</small>
                                <div class="small text-info font-weight-bold">
                                    {{ $interview_indicators['percentages']['notification_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center border-secondary h-100">
                            <div class="card-body py-3">
                                <h3 class="text-secondary mb-1">{{ $interview_indicators['totals']['pending'] }}</h3>
                                <small class="text-muted">Pendientes</small>
                                <div class="small text-secondary font-weight-bold">
                                    {{ $interview_indicators['percentages']['pending_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center border-danger h-100">
                            <div class="card-body py-3">
                                <h3 class="text-danger mb-1">{{ $interview_indicators['totals']['rejected'] }}</h3>
                                <small class="text-muted">Rechazados</small>
                                <div class="small text-danger">
                                    <i class="fas fa-star"></i> < 3
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="card text-center border-secondary h-100">
                            <div class="card-body py-3">
                                <h3 class="text-secondary mb-1">{{ $interview_indicators['totals']['with_siblings'] }}</h3>
                                <small class="text-muted">Con Hermanos</small>
                                <div class="small text-secondary font-weight-bold">
                                    {{ $interview_indicators['percentages']['siblings_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Estadísticas Detalladas --}}
                <div class="row">
                    {{-- Por Grado --}}
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-graduation-cap"></i> Por Grado/Año</h6>
                            </div>
                            <div class="card-body p-2">
                                @if($interview_indicators['by_grade']->count() > 0)
                                    @foreach($interview_indicators['by_grade']->take(5) as $grade)
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small>
                                                {{ $grade->grado ? $grade->grado->name : 'Grado ' . $grade->grade_year_aspiring }}
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

                    {{-- Por Calificación --}}
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-star"></i> Por Calificación</h6>
                            </div>
                            <div class="card-body p-2">
                                @if($interview_indicators['by_rating']->count() > 0)
                                    @foreach($interview_indicators['by_rating'] as $rating)
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small>
                                                @for($i = 1; $i <= $rating->rating; $i++)
                                                    <i class="fas fa-star text-warning"></i>
                                                @endfor
                                                @for($i = $rating->rating + 1; $i <= 5; $i++)
                                                    <i class="far fa-star text-muted"></i>
                                                @endfor
                                            </small>
                                            <span class="badge badge-{{ $rating->rating >= 4 ? 'success' : ($rating->rating >= 3 ? 'warning' : 'danger') }}">
                                                {{ $rating->total }}
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <small class="text-muted">No hay calificaciones disponibles</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Capacidad de Pago --}}
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-dollar-sign"></i> Capacidad de Pago</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <small><i class="fas fa-dollar-sign text-success"></i> Pueden pagar USD</small>
                                    <span class="badge badge-success">{{ $interview_indicators['payment_capacity']['can_pay_dollars'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <small><i class="fas fa-coins text-warning"></i> Pueden pagar Bs.</small>
                                    <span class="badge badge-warning">{{ $interview_indicators['payment_capacity']['can_pay_bolivars'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <small><i class="fas fa-handshake text-info"></i> Tienen garante</small>
                                    <span class="badge badge-info">{{ $interview_indicators['payment_capacity']['has_guarantor'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estadísticas Religiosas y Tendencias --}}
                <div class="row mt-3">
                    {{-- Estadísticas Religiosas --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cross"></i> Formación Católica</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <small>Conscientes del carácter católico</small>
                                    <span class="badge badge-primary">{{ $interview_indicators['religious_stats']['catholic_aware'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <small>Aceptan formación católica</small>
                                    <span class="badge badge-success">{{ $interview_indicators['religious_stats']['agrees_catholic_formation'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <small>Aceptan actividades católicas</small>
                                    <span class="badge badge-info">{{ $interview_indicators['religious_stats']['agrees_catholic_activities'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tendencia por Mes --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-chart-line"></i> Últimos 6 Meses</h6>
                            </div>
                            <div class="card-body p-2">
                                @if($interview_indicators['by_month']->count() > 0)
                                    @foreach($interview_indicators['by_month'] as $month)
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
                </div>

                {{-- Enlaces rápidos --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>Enlaces rápidos:</strong></span>
                                <div>
                                    <a href="{{ route('bienestars.matriculations.interviews.index') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-list"></i> Ver todas las entrevistas
                                    </a>
                                    @if($representant_ci)
                                        <a href="{{ route('bienestars.matriculations.interviews.index', ['representant_ci' => $representant_ci]) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-filter"></i> Entrevistas filtradas
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
