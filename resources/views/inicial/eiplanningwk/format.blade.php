{{--
    Formato imprimible de la Planificación Semanal (Educación Inicial).

    Controlador: App\Http\Controllers\Inicial\Tab\EiplanningwkController::format
    Ruta:        inicials.eiplanningwks.format  →  /app/inicials/eiplanningwks/{id}/format
    Port de:     saefl/s2526/resources/views/livewire/inicial/formats/eiplanningwk/index.blade.php

    Adaptaciones frente al legacy:
      · `as_replace($texto)` + el echo crudo de Blade → `nl2br(e($texto))`: el helper no
        existía en cfla y trataba el texto del docente como HTML cruto (XSS).
      · `Session::get('pescolar_name')` → `$pescolar?->name`: en cfla no se
        guarda ese valor de sesión; el período se lee del modelo.
      · Cabecera del bloque de estrategias: la legacy imprimía el membrete y el
        título "Estrategias del Docente" DOS veces (duplicado visible en el PDF
        impreso); aquí va una sola vez.
      · Se respeta `colspan` real (la legacy imprimía `colspan="6"` sobre una
        tabla de 6 columnas más una de momento = 7).
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planificación Semanal - Formato 1</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-transform: uppercase; }
        h1, h2, h4 { text-align: center; color: #2c3e50; margin-bottom: 0.2rem; vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .format table { border: 1px solid #ccc; }
        .format th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; vertical-align: top; }
        .format th { background-color: #ccc; color: white; }
        td { font-size: 0.6rem; }
        .membrete { text-align: center; font-weight: bold; margin-bottom: 20px; }
        .cabecera { margin-bottom: 20px; font-size: 0.8rem; border: 1px solid #ccc; }
        .cabecera p { margin: 5px 0; }
        .cabecera span { font-weight: bold; margin: 5px 0; }
        .page-break { page-break-after: always; page-break-inside: avoid; }
        .title { font-weight: bold; font-size: 14px; }
        @media print { body { text-transform: uppercase; } }
    </style>
</head>
<body>

    @php
        $grado = $eiplanningwk->grado;
        $seccion = $eiplanningwk->seccion;
        $manager = $eiplanningwk->manager;
        $peducativo = $eiplanningwk->peducativo;
        $membreteTitulo = 'PLAN SEMANAL';
    @endphp

    {{-- ═══ MEMBRETE (partial compartido por los 3 bloques) ═══ --}}
    @include('inicial.shared.membrete', ['titulo' => $membreteTitulo])

    {{-- ═══ 1 · CABECERA ═══ --}}
    <table class="cabecera format" width="100%" cellpadding="0" cellspacing="0"
        style="font-size:0.8rem;margin-bottom:0.2rem;padding-bottom:0.2rem;">
        <tr>
            <td style="width: 40%">
                <p><span>Docente </span>: {{ $profesor?->full_name ?? '—' }}</p>
                <p><span>Grupo </span>: {{ $grado?->name ?? '—' }}</p>
                <p><span>Sección </span>: {{ $seccion?->name ?? '—' }}</p>
                @if ($eiplanningwk->pensum)
                    <p><span>Área </span>: {{ $eiplanningwk->pensum->asignatura?->name ?? '—' }}</p>
                @endif
            </td>
            <td>
                <p><span>Fecha de Inicio </span>: {{ $eiplanningwk->finicial }}</p>
                <p><span>Fecha de Culminación </span>: {{ $eiplanningwk->ffinal }}</p>
                <p><span>Tiempo de ejecución Semana </span>: {{ $eiplanningwk->tiempo_ejecucion }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p><span>Diagnóstico </span>: {!! nl2br(e($eiplanningwk->diagnostico)) !!}.</p>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- ═══ 2 · TABLA RESUMEN POR ÁREA ═══ --}}
    @include('inicial.shared.membrete', ['titulo' => $membreteTitulo])
    <h4>Tabla Resumen</h4>

    <table class="format" width="100%" cellpadding="0" cellspacing="0"
        style="font-size:0.8rem;margin-bottom:0.2rem;padding-bottom:0.2rem;">
        <thead>
            <tr>
                <th>Área de Aprendizaje</th>
                <th>Componente</th>
                <th>Objetivo</th>
                <th>Aprendizaje Esperado</th>
                <th>Indicadores</th>
                <th>Línea de Investigación</th>
                <th>Énfasis Curriculares</th>
            </tr>
        </thead>
        <tbody>
            @php
                $eiplanningwsummaries = $eiplanningwk->getOrderedSummaries();
            @endphp

            @forelse ($eiplanningwsummaries as $item)
                <tr>
                    <td>{{ $item->pevaluacion?->pensum?->asignatura?->name ?? '—' }}</td>
                    <td>{!! nl2br(e($item->componente)) !!}</td>
                    <td>{!! nl2br(e($item->objetivo)) !!}</td>
                    <td>{!! nl2br(e($item->aprendizaje_esperado)) !!}</td>
                    <td>{!! nl2br(e($item->indicadores)) !!}</td>
                    {{-- Estas dos columnas son POR RESUMEN, no del proyecto: el legacy
                         las pintaba solo en la primera fila con `rowspan="{{ $rowspan }}"`,
                         así que los resúmenes 2..N imprimían celdas vacías. --}}
                    <td>{!! nl2br(e($item->linea_investigacion)) !!}</td>
                    <td>{!! nl2br(e($item->enfasis_curriculares)) !!}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay datos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- ═══ 3 · ESTRATEGIAS DEL DOCENTE (10 momentos × 5 días) ═══ --}}
    @include('inicial.shared.membrete', ['titulo' => $membreteTitulo])
    <h4>Estrategias del Docente</h4>

    <table class="format" style="font-size:0.8rem;margin-bottom:0.2rem;padding-bottom:0.2rem;">
        <thead>
            <tr>
                <th style="white-space: nowrap !important;">Momento de la Rutina Diaria</th>
                @foreach ($eiplanningwk->week_days as $day_key => $day_name)
                    <th>{{ $day_name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($eiplanningwk->list_moment as $momento_key => $momento_name)
                <tr>
                    <td>{!! nl2br(e($momento_name)) !!}</td>

                    @foreach ($eiplanningwk->week_days as $day_key => $day_name)
                        <td>
                            @php $estrategia = $eiplanningwk->getStrategyByMomentAndDay($momento_key, $day_key); @endphp

                            @if ($estrategia)
                                {!! nl2br(e($estrategia->estrategia)) !!}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach

            <tr>
                {{-- 6 columnas reales: 1 de momento + 5 de día. El legacy
                     imprimía `colspan="7"` aquí y la fila se desbordaba una
                     columna al exportar a PDF. --}}
                <td colspan="6">Observación <small>[Coord. Evaluación]</small>: {{ $eiplanningwk->observacion }}</td>
            </tr>
            <tr>
                <td colspan="6">Firma del Docente:</td>
            </tr>
        </tbody>
    </table>

    <footer>
        <div style="font-size:0.6rem;margin-top:3rem;">
            <span style="font-size:0.7rem;">
                Elaborado por: {{ Auth::user()->full_name ?? '' }} - SAEFL : {{ $fecha ?? '' }}
            </span>
            <div>
                Coordinador {{ $peducativo?->name }}: {{ $manager?->full_name }}
            </div>
        </div>
    </footer>

</body>
</html>
