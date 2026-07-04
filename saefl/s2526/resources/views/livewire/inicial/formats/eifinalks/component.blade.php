@foreach($eifinalks_component as $eifinalk)
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
                {{-- @if (!$pevaluacion->status_official)
                    <span class="text-secondary small text-uppercase">Componente de Formación</span>
                @endif --}}
            </div>

            <div class="row">
                @if (! empty($eifinalk->specialist_observation))
                {{-- Observaciones --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-title mt-3">
                            <i class="fas fa-clipboard-list me-2"></i>OBSERVACIÓN DEL ESPECIALISTA
                        </div>
                        <div class="content-block">
                            <div class="content-text">{!! nl2br(e($eifinalk->specialist_observation)) !!}</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- @if (!$loop->last) <div style="page-break-after:always;"></div> @endif --}}

    @endforeach
