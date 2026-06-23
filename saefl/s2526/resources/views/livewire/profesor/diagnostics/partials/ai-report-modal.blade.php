@if ($SessionModalReport && $selectedReport)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95% !important;">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-dark text-white py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-file-alt mr-2 text-warning"></i> INFORME DIAGNÓSTICO INTEGRAL
                    </h5>
                    <button type="button" class="close text-white" wire:click="closeSessionModalReport">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4 bg-light" style="max-height: 85vh; overflow-y: auto;">

                    <!-- Watermark -->
                    <div
                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.05; font-size: 8rem; font-weight: bold; pointer-events: none; z-index: 0;">
                        CONFIDENCIAL
                    </div>

                    <!-- Header Oficial -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <h3 class="font-weight-bold text-primary text-uppercase mb-1">Informe Diagnóstico Individual
                        </h3>
                        {{-- <h6 class="text-muted font-italic">Sistema de Diagnóstico Educativo Asistido por IA</h6> --}}
                        <div class="badge badge-light mt-2 px-3 py-2 text-muted border">
                            <i class="far fa-clock mr-1"></i> Generado el
                            {{ $selectedReport->generated_at ? $selectedReport->generated_at->format('d/m/Y h:i A') : 'N/A' }}
                        </div>
                    </div>

                    @php
                        /** ----------------------------------------------------------------
                         * 1. Decode & safety
                         * ---------------------------------------------------------------- */
                        $draftRaw = $selectedReport->latestDraft->output_text ?? '{}'; //dd($draftRaw);
                        $draft = json_decode($draftRaw, true);

                        if (!is_array($draft)) {
                            $draft = [];
                        }

                        /** ----------------------------------------------------------------
                         * 2. Global Results
                         * ---------------------------------------------------------------- */
                        $globalResults = $draft['global_results'] ?? [];

                        $aiAnalysis = $globalResults['global'] ?? null;
                        if (is_array($aiAnalysis)) {
                            $aiAnalysis = json_encode($aiAnalysis, JSON_UNESCAPED_UNICODE);
                        }

                        $aiPrecision = $globalResults['precision'] ?? null;
                        $aiAnswered = $globalResults['total_answered_questions'] ?? null;
                        $aiOpenLevel = $globalResults['open_ended_response_level'] ?? null;

                        /** ----------------------------------------------------------------
                         * 3. Areas
                         * ---------------------------------------------------------------- */
                        $aiAreas = is_array($draft['areas'] ?? null) ? $draft['areas'] : []; //dd($aiAreas, $draft['areas'] );

                        /** ----------------------------------------------------------------
                         * 4. Recommendations
                         * ---------------------------------------------------------------- */
                        $aiRecommendations = is_array($draft['recommendations'] ?? null)
                            ? $draft['recommendations']
                            : [];

                        /** ----------------------------------------------------------------
                         * 5. Profile
                         * ---------------------------------------------------------------- */
                        $profile = is_array($draft['profile'] ?? null) ? $draft['profile'] : [];

                        $aiStrengths = $profile['strengths'] ?? null;
                        $aiNeeds = $profile['needs'] ?? null;
                        $aiAttitudinalFactors = $profile['attitudinal_factors'] ?? null;
                        $aiConclusion = $profile['cognitive_summary'] ?? null;
                        $aiBarriers = $profile['potential_barriers'] ?? null;
                        $aiStyles = $profile['processing_styles'] ?? null;
                        $aiLearningStyles = $profile['learning_styles'] ?? null;

                        /** ----------------------------------------------------------------
                         * Icon Logic for Styles
                         * ---------------------------------------------------------------- */
                        $styleIcon = 'fas fa-brain'; // Default
                        $sLower = mb_strtolower($aiStyles ?? '');
                        if (str_contains($sLower, 'empirista') || str_contains($sLower, 'inductivo')) {
                            $styleIcon = 'fas fa-microscope';
                        } elseif (str_contains($sLower, 'racionalista') || str_contains($sLower, 'deductivo')) {
                            $styleIcon = 'fas fa-cogs';
                        } elseif (str_contains($sLower, 'introspectivo') || str_contains($sLower, 'vivencial')) {
                            $styleIcon = 'fas fa-heart';
                        }

                        $learningIcon = 'fas fa-lightbulb'; // Default
                        $lLower = mb_strtolower($aiLearningStyles ?? '');
                        if (str_contains($lLower, 'visual')) {
                            $learningIcon = 'fas fa-eye';
                        } elseif (str_contains($lLower, 'auditivo')) {
                            $learningIcon = 'fas fa-headphones';
                        } elseif (str_contains($lLower, 'kinestésico') || str_contains($lLower, 'cinestésico')) {
                            $learningIcon = 'fas fa-running';
                        }

                        /** ----------------------------------------------------------------
                         * 6. Contrast
                         * ---------------------------------------------------------------- */
                        $aiContrast = is_array($draft['contrast'] ?? null) ? $draft['contrast'] : [];
                    @endphp



                    <!-- 1. IDENTIFICACIÓN -->
                    <div class="card shadow-sm mb-4 border-0 overflow-hidden" style="border-radius: 12px;">
                        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between"
                            style="border-bottom: 2px solid #f8f9fc;">
                            <h6 class="m-0 font-weight-bold text-primary text-uppercase tracking-wide">
                                <i class="fas fa-id-card-alt mr-2"></i>1. Identificación del Estudiante
                            </h6>
                            <span class="badge badge-primary-soft text-primary px-3 py-2"
                                style="background-color: #e0e7ff; border-radius: 50px;">
                                <i class="fas fa-university mr-1 small"></i> Informe Institucional
                            </span>
                        </div>

                        <div class="card-body bg-white">
                            <div class="row align-items-center">
                                <div class="col-md-auto text-center mb-3 mb-md-0">
                                    <div class="avatar-circle shadow-sm d-flex align-items-center justify-content-center bg-light border"
                                        style="width: 80px; height: 80px; border-radius: 50%; margin: 0 auto;">
                                        <i class="fas fa-user fa-2x text-dark"></i>
                                    </div>
                                </div>

                                <div class="col-md pl-md-4">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <label
                                                class="text-xs text-uppercase font-weight-bold text-muted mb-1 d-block"
                                                style="letter-spacing: 0.5px;">
                                                Nombre Completo
                                            </label>
                                            <h5 class="font-weight-bold text-gray-900 mb-2">
                                                {{ $selectedReport->estudiant->full_name }}
                                            </h5>

                                            <div class="d-flex align-items-center">
                                                <div class="badge badge-light border text-dark px-2 py-1 mr-2">
                                                    <i class="fas fa-fingerprint text-primary mr-1"></i>
                                                    {{ $selectedReport->estudiant->ci_estudiant }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 border-left-md">
                                            <div class="mb-3">
                                                <label
                                                    class="text-xs text-uppercase font-weight-bold text-muted mb-1 d-block"
                                                    style="letter-spacing: 0.5px;">
                                                    Grado y Sección
                                                </label>
                                                <p class="text-gray-800 mb-0 font-weight-bold">
                                                    <i class="fas fa-layer-group text-info mr-1"></i>
                                                    {{ $selectedReport->estudiant->grado->name ?? 'N/A' }}
                                                    <span class="text-muted mx-1">|</span>
                                                    Sección: {{ $selectedReport->estudiant->seccion->name ?? 'N/A' }}
                                                </p>
                                            </div>

                                            <div>
                                                <label
                                                    class="text-xs text-uppercase font-weight-bold text-muted mb-1 d-block"
                                                    style="letter-spacing: 0.5px;">
                                                    Referente Normativo
                                                </label>
                                                <p class="text-gray-700 mb-0 small italic">
                                                    <i class="fas fa-bookmark text-warning mr-1"></i>
                                                    {{ $selectedReport->referent->name ?? 'N/A' }}
                                                    <span class="badge badge-secondary badge-pill ml-1">
                                                        v{{ $selectedReport->referent->version ?? '1.0' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        /* Estilos extra para pulir el acabado */
                        .text-xs {
                            font-size: 0.75rem;
                        }

                        .border-left-md {
                            border-left: 1px solid #eaecf4;
                        }

                        @media (max-width: 767px) {
                            .border-left-md {
                                border-left: none;
                                border-top: 1px solid #eaecf4;
                                padding-top: 15px;
                            }
                        }

                        .avatar-circle {
                            transition: all 0.3s ease;
                        }

                        .card:hover .avatar-circle {
                            background-color: #4e73df !important;
                            color: white !important;
                        }

                        .card:hover .avatar-circle i {
                            color: white !important;
                        }
                    </style>

                    <!-- 2. CONTEXTO -->
                    <div class="alert alert-info shadow-sm mb-4 border-0" role="alert">
                        <h6 class="alert-heading font-weight-bold text-uppercase">
                            <i class="fas fa-info-circle mr-2"></i>2. Contexto del Diagnóstico
                        </h6>
                        <p class="mb-0 text-justify">
                            <strong>Instrumento Aplicado:</strong>
                            {{ $selectedReport->diagMain->name ?? 'Diagnóstico General' }}.
                            <strong>Propósito:</strong> Identificar el nivel de dominio de competencias previas
                            esenciales.
                        </p>
                        <hr>
                        <p class="mb-0 small font-italic">Este informe se basa exclusivamente en evidencias recolectadas
                            mediante instrumentos estandarizados.</p>
                    </div>

                    <!-- 3. RESULTADOS GLOBALES -->
                    <style>
                        .report-card {
                            border: none;
                            border-radius: 12px;
                            transition: transform 0.2s;
                        }

                        .report-card:hover {
                            transform: translateY(-2px);
                        }

                        .bg-soft-success {
                            background-color: #e8f5e9;
                            color: #2e7d32;
                        }

                        .bg-soft-warning {
                            background-color: #fff3e0;
                            color: #ef6c00;
                        }

                        .bg-soft-info {
                            background-color: #e3f2fd;
                            color: #1565c0;
                        }

                        .text-justify {
                            text-justify: inter-word;
                            line-height: 1.6;
                        }

                        .border-left-lg {
                            border-left: 5px solid !important;
                        }

                        .list-custom li {
                            position: relative;
                            padding-left: 20px;
                            margin-bottom: 8px;
                        }

                        .list-custom li::before {
                            content: "•";
                            position: absolute;
                            left: 0;
                            font-weight: bold;
                        }

                        .chart-container {
                            background: #f8f9fc;
                            border-radius: 15px;
                            padding: 20px;
                        }
                    </style>

                    <div class="card report-card shadow-sm mb-4 border-left-lg border-success">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary text-uppercase">
                                <i class="fas fa-th-list mr-2"></i>3. RESULTADOS GLOBALES
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center border-right">
                                    <div class="display-4 font-weight-bold text-success mb-0">
                                        {{ number_format($aiPrecision ?? 0, 1) }}%</div>
                                    <p class="text-uppercase small font-weight-bold text-muted mb-0">Precisión Global
                                    </p>
                                    <div class="progress mt-3 shadow-sm" style="height: 8px; border-radius: 10px;">
                                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                            style="width: {{ $aiPrecision }}%"></div>
                                    </div>
                                </div>
                                <div class="col-md-9 pl-md-4">
                                    <div class="p-3 rounded bg-light border">
                                        <p class="text-dark mb-0 text-justify italic">
                                            "{!! markdown_to_bootstrap($aiAnalysis) ?: 'Análisis global pendiente de generación.' !!}"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Desglose por Áreas de Formación --}}
                    <style>
                        /* Estilos de UI Moderna para el Reporte */
                        .report-modal-content {
                            border-radius: 15px;
                            border: none;
                        }

                        .report-card {
                            border: none;
                            border-radius: 12px;
                            transition: transform 0.2s;
                        }

                        .bg-soft-success {
                            background-color: #f0fdf4;
                            color: #166534;
                        }

                        .bg-soft-warning {
                            background-color: #fffbeb;
                            color: #92400e;
                        }

                        .bg-soft-danger {
                            background-color: #fef2f2;
                            color: #991b1b;
                        }

                        .bg-soft-info {
                            background-color: #f0f9ff;
                            color: #075985;
                        }

                        .text-justify {
                            text-align: justify !important;
                            text-justify: inter-word;
                        }

                        .leading-relaxed {
                            line-height: 1.6;
                        }

                        .shadow-xs {
                            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                        }

                        .border-left-lg {
                            border-left: 5px solid !important;
                        }

                        .avatar-circle {
                            transition: all 0.3s ease;
                        }

                        .area-item:hover {
                            background-color: #f8fafc;
                        }

                        .x-small {
                            font-size: 0.7rem;
                        }
                    </style>

                    <div class="bg-light ">
                        <div class="card report-card shadow-sm mb-4">
                            <div
                                class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                                <h6 class="m-0 font-weight-bold text-warning text-uppercase">
                                    <i class="fas fa-th-list mr-2"></i>4. Análisis por Área de Formación
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                @forelse ($aiAreas as $area)
                                    @php
                                        $levelRaw = strtoupper($area['level'] ?? 'MEDIUM');
                                        $levelConfig = [
                                            'HIGH' => [
                                                'color' => 'success',
                                                'icon' => 'fa-check-circle',
                                                'label' => 'Nivel Alto',
                                            ],
                                            'MEDIUM' => [
                                                'color' => 'warning',
                                                'icon' => 'fa-pause-circle',
                                                'label' => 'Nivel Medio',
                                            ],
                                            'LOW' => [
                                                'color' => 'danger',
                                                'icon' => 'fa-exclamation-triangle',
                                                'label' => 'Nivel Bajo',
                                            ],
                                        ];
                                        $config =
                                            $levelConfig[$levelRaw] ??
                                            ($levelRaw == 'ALTO'
                                                ? $levelConfig['HIGH']
                                                : ($levelRaw == 'BAJO'
                                                    ? $levelConfig['LOW']
                                                    : $levelConfig['MEDIUM']));
                                    @endphp
                                    <div class="area-item p-4 border-bottom">
                                        <div class="row">
                                            <div class="col-lg-6 mb-3 mb-lg-0">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="bg-white border shadow-sm text-{{ $config['color'] }} rounded-circle mr-2 d-flex align-items-center justify-content-center"
                                                        style="width: 35px; height: 35px; min-width: 35px;">
                                                        <i class="fas {{ $config['icon'] }} small"></i>
                                                    </div>
                                                    <h6 class="font-weight-bold text-dark mb-0">
                                                        {{ $area['area_name'] ?? 'Área' }}</h6>
                                                </div>
                                                <span
                                                    class="badge badge-{{ $config['color'] }} px-2 mb-2 x-small shadow-xs">{{ $config['label'] }}</span>
                                                <p
                                                    class="small text-muted text-justify mt-2 leading-relaxed italic border-left pl-2">
                                                    {{ $area['observation'] ?? 'Sin observación.' }}
                                                </p>
                                            </div>
                                            <div class="col-lg-3 mb-3 mb-lg-0 border-left-light px-lg-4">
                                                <p class="x-small font-weight-bold text-success text-uppercase mb-2"><i
                                                        class="fas fa-star mr-1"></i> Fortalezas</p>
                                                <ul class="list-unstyled x-small text-secondary">
                                                    @foreach ($area['strengths'] ?? ['N/A'] as $s)
                                                        <li class="d-flex mb-1"><i
                                                                class="fas fa-plus-circle text-success mr-2 mt-1"></i>{{ $s }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="col-lg-3 border-left-light px-lg-4">
                                                <p class="x-small font-weight-bold text-danger text-uppercase mb-2"><i
                                                        class="fas fa-tools mr-1"></i> A Reforzar</p>
                                                <ul class="list-unstyled x-small text-secondary">
                                                    @foreach ($area['weaknesses'] ?? ['N/A'] as $w)
                                                        <li class="d-flex mb-1"><i
                                                                class="fas fa-chevron-right text-danger mr-2 mt-1"></i>{{ $w }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-5 text-center text-muted italic small">No hay datos por área.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card report-card shadow-sm mb-4 bg-light border">
                            <div class="card-body py-2 px-3">
                                <p class="mb-0 x-small text-muted font-italic">
                                    <i class="fas fa-info-circle mr-1"></i> 5. Contraste Currículo vs Evidencia:
                                    Información no especificada.
                                </p>
                            </div>
                        </div>
                    </div>

                    @php
                        $labels = [];
                        $data = [];
                        $colors = [];
                        $palette = [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                        ];
                        if (!empty($aiAreas)) {
                            foreach (array_values($aiAreas) as $i => $area) {
                                $labels[] = $area['area_name'] ?? 'Área ' . ($i + 1);
                                $data[] = $area['precision'] ?? 0;
                                $colors[] = $palette[$i % count($palette)];
                            }
                        }
                    @endphp

                    <div class="card report-card shadow-sm mb-4 border-left-lg border-info">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-info text-uppercase">
                                6. Perfil Diagnóstico y Cognitivo
                            </h6>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="bg-soft-info p-3 rounded mb-4">
                                        <h6 class="font-weight-bold mb-2 small"><i
                                                class="fas fa-comment-alt mr-2"></i>Conclusión Diagnóstica:</h6>
                                        <p class="mb-0 small text-justify font-italic">
                                            {{ $aiConclusion ?? 'Información no disponible.' }}</p>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <div class="p-3 border rounded text-center shadow-xs bg-white">
                                                <i class="fas fa-brain text-dark fa-2x mb-2"></i>
                                                <p class="small text-muted mb-1 text-uppercase font-weight-bold">
                                                    Estilo de pensamiento predominante:</p>
                                                <span
                                                    class="d-block text-uppercase font-weight-bold text-dark">{{ $aiStyles ?? 'Estándar' }}</span>
                                                <div class="">
                                                    <i
                                                        class="{{ $styleIcon ?? null }} text-info fa-3x mb-2 border rounded p-2 m-2 shadow-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 border rounded text-center shadow-xs bg-white">
                                                <i class="fas fa-lightbulb text-dark fa-2x mb-2"></i>
                                                <p class="small text-muted mb-1 text-uppercase font-weight-bold">
                                                    Estilo de pprendizaje predominante:</p>
                                                <span
                                                    class="d-block text-uppercase font-weight-bold text-dark">{{ $aiLearningStyles ?? 'Estándar' }}</span>
                                                <div class=""><i
                                                        class="{{ $learningIcon ?? null }} text-warning fa-3x mb-2 border rounded p-2 m-2 shadow-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row small">
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="font-weight-bold text-uppercase text-muted x-small d-block mb-1">Fortalezas
                                                Transversales</label>
                                            <p class="p-2 border-left border-success bg-light rounded text-dark">
                                                {{ $aiStrengths ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="font-weight-bold text-uppercase text-muted x-small d-block mb-1">Necesidades
                                                de Apoyo</label>
                                            <p class="p-2 border-left border-primary bg-light rounded text-dark">
                                                {{ $aiNeeds ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="font-weight-bold text-uppercase text-muted x-small d-block mb-1">Barreras
                                                Potenciales</label>
                                            <p class="p-2 border-left border-danger bg-light rounded text-dark">
                                                {{ $aiBarriers ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="font-weight-bold text-uppercase text-muted x-small d-block mb-1">Factores
                                                Actitudinales</label>
                                            <p class="p-2 border-left border-warning bg-light rounded text-dark">
                                                {{ $aiAttitudinalFactors ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (count($aiContrast) > 0)
                        <div class="card shadow-sm mb-4 border-left-secondary">
                            <div class="card-header py-2 bg-white">
                                <h6 class="m-0 font-weight-bold text-secondary text-uppercase">
                                    Contrastes y Brechas Identificadas
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach ($aiContrast as $c)
                                    <p class="text-justify text-muted">
                                        {{ $c['gaps'] ?? 'Brecha no especificada.' }}
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 7. RECOMENDACIONES -->
                    <div class="card shadow-sm mb-5 border-left-danger">
                        <div class="card-header py-2 bg-white">
                            <h6 class="m-0 font-weight-bold text-danger text-uppercase">
                                7. Recomendaciones Pedagógicas
                            </h6>
                        </div>

                        <div class="card-body p-0">
                            @if (is_array($aiRecommendations) && count($aiRecommendations) > 0)
                                @php
                                    $groupedRecommendations = collect($aiRecommendations)->groupBy(function ($rec) {
                                        return is_array($rec) ? strtoupper($rec['type'] ?? 'GENERAL') : 'GENERAL';
                                    });
                                @endphp

                                @foreach ($groupedRecommendations as $type => $recs)
                                    <div
                                        class="bg-light px-3 py-2 border-bottom {{ !$loop->first ? 'border-top' : '' }}">
                                        <h6 class="m-0 font-weight-bold text-dark text-uppercase">
                                            <i class="fas fa-tag mr-2 text-danger"></i>DIRIGIDO A: {{ $type }}
                                        </h6>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($recs as $rec)
                                            @php
                                                $priority = is_array($rec)
                                                    ? strtoupper($rec['priority'] ?? 'MEDIUM')
                                                    : 'MEDIUM';
                                                $text = is_array($rec) ? $rec['recommendation'] ?? null : $rec;

                                                $priorityClass = match ($priority) {
                                                    'HIGH' => 'danger',
                                                    'MEDIUM' => 'warning',
                                                    'LOW' => 'info',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <li class="list-group-item border-0 px-4 py-3">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                                                    <div class="w-100">
                                                        <div class="mb-1">
                                                            <span
                                                                class="badge badge-{{ $priorityClass }} x-small px-2">
                                                                Prioridad {{ ucfirst(strtolower($priority)) }}
                                                            </span>
                                                        </div>
                                                        <div class="text-dark text-justify small leading-relaxed">
                                                            {!! markdown_to_bootstrap($text ?? 'Recomendación no especificada.') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            @else
                                <div class="p-4 text-center text-muted small">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    No se generaron recomendaciones pedagógicas para este diagnóstico.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 8. VALIDACIÓN -->
                    {{-- <div class="mt-5">
                        <h6 class="text-uppercase text-gray-500 font-weight-bold mb-4 border-bottom pb-2">8. Validación
                            Institucional</h6>

                        <div class="row text-center mt-5">
                            <div class="col-6">
                                <div class="border-top border-dark w-75 mx-auto pt-2">
                                    <strong>Docente Guía / Especialista</strong><br>
                                    <span class="small text-muted">Firma y Sello</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border-top border-dark w-75 mx-auto pt-2">
                                    <strong>Coordinación Académica</strong><br>
                                    <span class="small text-muted">Firma y Sello</span>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Footer Info -->
                    <div class="mt-4 pt-3 border-top text-center text-muted small bg-white">
                        <p class="mb-1">
                            <span class="badge badge-light border">Declaración Institucional</span> Este informe ha
                            sido generado con asistencia de inteligencia artificial bajo estrictos protocolos
                            pedagógicos.
                        </p>
                        <p class="mb-0 font-monospace text-xs" style="font-size: 0.7em;">
                            HASH: {{ $selectedReport->latestDraft->input_hash ?? 'N/A' }} <span
                                class="mx-2">|</span>
                            MODELO: {{ $selectedReport->latestDraft->llm_model ?? 'N/A' }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
@endif
