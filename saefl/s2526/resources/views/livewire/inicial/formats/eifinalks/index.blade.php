<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Pedagógico Parcial - Educación Inicial</title>
    <link href="{{ asset('vendor/bootstrap/5.3.0/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/einicial/format.css') }}" rel="stylesheet">
</head>
<body class="container-fluid py-2 px-1">
    @php
        $pevaluacion = $eifinalk->pevaluacion;
        $estudiant = $eifinalk->estudiant;
        $grado = $pevaluacion->pensum->grado;
        $seccion = $pevaluacion->seccion;
        $lapso = $pevaluacion->lapso;
        $profesor = $pevaluacion->profesor;
    @endphp

    @include('livewire.inicial.formats.eifinalks.membrete')

    <div class="header text-center text-uppercase">
        <h4 class="mb-2 fw-bold">BOLETÍN INFORMATIVO PARCIAL PEDAGÓGICO, ÁREA DE APRENDIZAJE: {{ $pevaluacion->asignatura->name ?? 'Sin área de formación' }}</h4>
        <div class="h5 text-muted small">
            <b>{{ $eifinalk->title }}</b>
            <br>
            <span class="text-muted small">
                <b>Docente:</b> {{ $profesor->fullname }}
            </span>
        </div>
    </div>

    {{-- Información Básica --}}
    <div class="section">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Estudiante</div>
                <div class="info-value">{{ $estudiant->fullname2 }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Cédula</div>
                <div class="info-value">{{ $estudiant->ci_estudiant }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Grupo</div>
                <div class="info-value">{{ $grado->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Sección</div>
                <div class="info-value">{{ $seccion->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Lapso</div>
                <div class="info-value">{{ $lapso->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Docente</div>
                <div class="info-value">{{ $profesor->fullname }}</div>
            </div>
        </div>
    </div>

    {{-- Contexto y Planificación y Proyecto significativo --}}
    <div class="row">
        {{-- Contexto y Planificación --}}
        <div class="col-md-6">
            <div class="section-title">
                <i class="fas fa-users me-2"></i>CONTEXTO Y PLANIFICACIÓN
            </div>
            <div class="content-block">
                <div class="content-label">Apreciación del estudiante</div>
                <div class="content-text">{!! nl2br(e($eifinalk->context_group)) !!}</div>
            </div>

            <div class="content-block">
                <div class="content-label">Planificación ejecutada</div>
                <div class="content-text">{!! nl2br(e($eifinalk->planing_eject)) !!}</div>
            </div>

            <div class="content-block">
                <div class="content-label">Proyecto significativo</div>
                <div class="content-text">{!! nl2br(e($eifinalk->featured_project)) !!}</div>
            </div>
        </div>

        {{-- Actividades y Logros --}}
        <div class="col-md-6">
            <div class="section-title">
                <i class="fas fa-tasks me-2"></i>ACTIVIDADES Y LOGROS
            </div>
            <div class="content-block">
                <div class="content-label">Eventos especiales</div>
                <div class="content-text">{!! nl2br(e($eifinalk->special_activities)) !!}</div>
            </div>

            <div class="content-block">
                <div class="content-label">Logros del estudiante</div>
                <div class="content-text">{!! nl2br(e($eifinalk->achievements)) !!}</div>
            </div>

            <div class="content-block">
                <div class="content-label">Participación familiar</div>
                <div class="content-text">{!! nl2br(e($eifinalk->family_participation)) !!}</div>
            </div>
        </div>
    </div>


    {{-- Aprendizajes Esperados y Observaciones --}}
    <div class="row">
        <div class="col-md-6">
            {{-- Aprendizajes Esperados --}}
            <div class="section-title mt-3">
                <i class="fas fa-book-open me-2"></i>APRENDIZAJES ESPERADOS
            </div>
            @foreach($eifinalk->expectations->groupBy('area.name') as $areaName => $expectations)
                <div class="learning-area">
                    <div class="area-title">{{ $areaName }}</div>
                    @foreach($expectations as $expectation)
                        <div class="expectation-item">
                            <i class="fas fa-check"></i>
                            {{ $expectation->description }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="col-md-6">
                {{-- Observaciones --}}
                <div class="section-title mt-3">
                    <i class="fas fa-clipboard-list me-2"></i>OBSERVACIONES
                </div>
                <div class="content-block">
                    <div class="content-label">Observaciones socioafectivas</div>
                    <div class="content-text">{!! nl2br(e($eifinalk->individual_observations)) !!}</div>
                </div>
                <div class="content-block">
                    <div class="content-label">Reflexión final del docente</div>
                    <div class="content-text">{!! nl2br(e($eifinalk->conclusions)) !!}</div>
                </div>
                <div class="content-block">
                    <div class="content-label">Sugerencias</div>
                    <div class="content-text">{!! nl2br(e($eifinalk->recommendations)) !!}</div>
                </div>
        </div>
    </div>

    {{-- Firmas --}}
    <div class="signature-section text-uppercase">
        <div class="row">
            <div class="col-6">
                <div class="signature-line"></div>
                <div class="text-center">Firma del Docente</div>
            </div>
            <div class="col-6">
                <div class="signature-line"></div>
                <div class="text-center">Firma del Director</div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-6">
                <div class="text-center">Fecha: {{ $fecha }}</div>
            </div>
            <div class="col-6">
                <div class="text-center">Fecha: _________________</div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer text-uppercase">
        <div class="row">
            <div class="col-12">
                <p class="mb-1">{{ $institucion->name ?? 'Institución Educativa' }}</p>
                <p class="mb-1">RIF: {{ $institucion->rif_institution ?? 'N/A' }}</p>
                <p class="mb-1">Dirección: {{ $institucion->address ?? 'N/A' }}</p>
                <p class="mt-2 text-muted">
                    <small>Documento generado el {{ $fecha }} por {{ Auth::user()->profile->full_name ?? 'Sistema' }}</small>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
