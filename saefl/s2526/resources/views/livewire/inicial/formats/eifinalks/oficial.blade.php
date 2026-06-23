@foreach ($eifinalks_oficial as $eifinalk)
    @php
        $pevaluacion = $eifinalk->pevaluacion;
        $asignatura = $pevaluacion->asignatura;
    @endphp

    <div class="report">
        {{-- Área de Aprendizaje --}}
        <div class="section-title">
            <i class="fas fa-file-alt me-2"></i>
            <span class="section-title-label">ÁREA DE APRENDIZAJE:</span>
            <span class="section-title-value">{{ $asignatura->name ?? 'Sin área de formación' }}</span>
            @if (!$pevaluacion->status_official)
                <span class="text-secondary small text-uppercase">Componente de Formación</span>
            @endif
        </div>

        <div class="row">
            {{-- Aprendizajes Esperados --}}
            <div class="col-md-12">
                @if (!empty($eifinalk->expected_learnings))
                    <div class="section-title mt-1">
                        <i class="fas fa-book-open me-2"></i>APRENDIZAJES ESPERADOS
                        <div class="info-label">
                            {{ $eifinalk->expected_learnings ?? null }}
                        </div>
                    </div>
                @endif
                @if (!empty($eifinalk->achievements))
                    <div class="section-title mt-1">
                        <i class="fas fa-book me-2"></i>LOGROS DEL ESTUDIANTE
                        <div class="info-label">
                            {{ $eifinalk->achievements ?? null }}
                        </div>
                    </div>
                @endif
            </div>

        </div>



    </div>

@endforeach

@foreach ($eifinalks_oficial as $eifinalk)
    @if (!empty($eifinalk->individual_observations))
        {{-- Observaciones --}}
        <div class="row">
            <div class="col-md-12">
                <div class="section-title mt-3">
                    <i class="fas fa-clipboard-list me-2"></i>OBSERVACIONES GENERALES
                </div>
                <div class="content-block">
                    <div class="content-text">{!! nl2br(e($eifinalk->individual_observations)) !!}</div>
                </div>
            </div>
        </div>
    @endif
@endforeach
