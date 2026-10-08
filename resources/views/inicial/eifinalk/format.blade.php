{{--
    Formato imprimible del INFORME FINAL por estudiante (Educación Inicial).

    Controlador: App\Http\Controllers\Inicial\Tab\EifinalkController::format
    Ruta:        inicials.eifinalks.format  →  /app/inicials/eifinalks/{id}/format

    ─────────────────────────────────────────────────────────────────────────────
    CAMPOS CONDICIONALES SEGÚN `status_official`
    ─────────────────────────────────────────────────────────────────────────────
    El tipo del informe no es un campo suyo: lo decide la CARGA ACADÉMICA
    (`pevaluacion.status_official`). Un informe oficial imprime logros,
    observaciones individuales y aprendizajes esperados; uno de componente
    imprime la observación del especialista. Se calcula una sola vez arriba y se
    usa para marcar las filas, en lugar de dejar bloques vacíos que el docente
    tiene que tachar a mano.

    Adaptaciones frente al legacy:
      · los textos se imprimen con `nl2br(e(...))` en lugar del helper
        `as_replace()` + el echo crudo de Blade, que no existe en cfla e
        interpretaba el contenido del docente como HTML crudo (XSS);
      · las expectativas se agrupan por ÁREA (el pivote desnormaliza
        `eilearningarea_id` justo para poder hacerlo sin joins extra);
      · el membrete es el partial COMPARTIDO `inicial.shared.membrete`.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Final - Formato 6</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-transform: uppercase; }
        h1, h2, h4 { text-align: center; color: #2c3e50; margin-bottom: 0.2rem; vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .format table { border: 1px solid #ccc; }
        .format th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; vertical-align: top; }
        .format th { background-color: #ccc; color: white; }
        td { font-size: 0.62rem; }
        .cabecera { margin-bottom: 20px; font-size: 0.8rem; border: 1px solid #ccc; }
        .cabecera p { margin: 5px 0; }
        .cabecera span { font-weight: bold; margin: 5px 0; }
        .page-break { page-break-after: always; page-break-inside: avoid; }
        .title { font-weight: bold; font-size: 14px; }
        .etiqueta { display: inline-block; min-width: 190px; font-weight: bold; }
        .oficial { background-color: #e8f4ec; }
        @media print { body { text-transform: uppercase; } }
    </style>
</head>
<body>

    @php
        $pevaluacion = $eifinalk->pevaluacion;
        $estudiante = $eifinalk->expectant;
        $oficial = (bool) ($pevaluacion?->status_official);

        // El pivote guarda `eilearningarea_id` justo para agrupar sin joins.
        $expectativasPorArea = $eifinalk->expectations
            ->groupBy('eilearningarea_id')
            ->sortBy(fn ($grupo, $areaId) => $grupo->first()->area?->name ?? '');

        // Bloques del informe, con su rótulo. Los condicionales se añaden a la lista
        // en vez de repartirse por el marcado: así el orden de impresión es
        // explícito y no depende del HTML.
        $bloques = [
            'context_group' => 'Contexto del grupo',
            'planing_eject' => 'Planeamiento',
            'featured_project' => 'Proyecto destacado',
            'special_activities' => 'Actividades especiales',
            'achievements' => 'Logros',
            'individual_observations' => 'Observaciones individuales',
            'family_participation' => 'Participación familiar',
            'conclusions' => 'Conclusiones',
            'recommendations' => 'Recomendaciones',
            'expected_learnings' => 'Aprendizajes esperados',
            'specialist_observation' => 'Observación del especialista',
        ];

        $aplicables = $oficial
            ? ['expected_learnings', 'achievements', 'individual_observations']
            : ['specialist_observation'];
    @endphp

    @include('inicial.shared.membrete', ['titulo' => 'INFORME FINAL'])

    <h4>{{ $eifinalk->title }}</h4>

    <table class="cabecera format" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 34%">
                <p><span>Estudiante</span>: {{ $estudiante?->full_name ?? '—' }}</p>
                <p><span>Cédula</span>: {{ $estudiante?->ci_estudiant ?? '—' }}</p>
            </td>
            <td>
                <p><span>Grupo</span>: {{ $pevaluacion?->pensum?->grado?->name ?? '—' }} ·
                    {{ $pevaluacion?->seccion?->name ?? '—' }}</p>
                <p><span>Lapso</span>: {{ $pevaluacion?->lapso?->name ?? '—' }}</p>
                <p><span>Área</span>: {{ $pevaluacion?->pensum?->asignatura?->name ?? '—' }}</p>
            </td>
            <td style="width: 24%">
                <p><span>Tipo</span>: {{ $oficial ? 'OFICIAL' : 'COMPONENTE' }}</p>
                <p><span>Orden</span>: {{ $eifinalk->order }}</p>
            </td>
        </tr>
    </table>

    {{-- ═══ 1 · EXPECTATIVAS DE APRENDIZAJE, POR ÁREA ═══ --}}
    <h4>Expectativas de Aprendizaje</h4>

    @forelse ($expectativasPorArea as $areaId => $delArea)
        <table class="format" width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem;">
            <thead>
                <tr>
                    <th style="width: 30%">Área de aprendizaje</th>
                    <th>Expectativas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $delArea->first()->area?->name ?? '—' }}</td>
                    <td>
                        @foreach ($delArea as $expectation)
                            <div>{{ $expectation->description }}</div>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    @empty
        <p style="font-size:0.8rem;">Sin expectativas seleccionadas.</p>
    @endforelse

    {{-- ═══ 2 · DESARROLLO DEL INFORME ═══ --}}
    <h4>Desarrollo</h4>

    <table class="format" width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem;">
        <tbody>
            @foreach ($bloques as $campo => $etiqueta)
                {{-- Los bloques que no aplican al tipo de informe no se imprimen:
                     el legacy los dejaba en blanco para tacharlos a mano. --}}
                @if (in_array($campo, $aplicables, true) || $eifinalk->{$campo})
                    @if (trim((string) $eifinalk->{$campo}) !== '')
                        <tr @class(['oficial' => $oficial && in_array($campo, $aplicables, true)])>
                            <td style="width: 30%"><span class="etiqueta">{{ $etiqueta }}</span></td>
                            <td>{!! nl2br(e($eifinalk->{$campo})) !!}</td>
                        </tr>
                    @endif
                @endif
            @endforeach
        </tbody>
    </table>

    <footer>
        <table class="format" width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem;">
            <tr>
                <td style="width: 50%">Firma del docente:</td>
                <td style="width: 25%">Firma del representante:</td>
                <td>Fecha: {{ $fecha }}</td>
            </tr>
        </table>

        <div style="font-size:0.6rem;margin-top:2rem;">
            <span style="font-size:0.7rem;">
                Elaborado por: {{ Auth::user()->full_name ?? '' }} - SAEFL : {{ $fecha ?? '' }}
            </span>
        </div>
    </footer>

</body>
</html>