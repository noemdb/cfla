{{--
    Formato imprimible del Plan de Evaluación (Educación Inicial).

    Controlador: App\Http\Controllers\Inicial\Tab\EievaluationkController::format
    Ruta:        inicials.eievaluationks.format  →  /app/inicials/eievaluationks/{id}/format
    Port de:     saefl/s2526/resources/views/livewire/inicial/formats/eievaluationk/index.blade.php

    ─────────────────────────────────────────────────────────────────────────────
    UNA HOJA POR ÁREA
    ─────────────────────────────────────────────────────────────────────────────
    A diferencia de los otros formatos (una hoja por bloque), aquí el legacy
    imprimía una PÁGINA COMPLETA por área de aprendizaje, con su membrete y su
    tabla de posiciones. Se mantiene: es lo que permite imprimir y archivar cada
    área por separado, que es como lo rellenan las docentes.

    Adaptaciones frente al legacy:
      · `as_replace($texto)` + el echo crudo de Blade → `nl2br(e($texto))`: el helper no
        existía en cfla y trataba el contenido del docente como HTML crudo (XSS).
      · el membrete es el partial COMPARTIDO (`inicial.shared.membrete`), que el
        legacy duplicaba en cada carpeta `formats/*`;
      · `$profesor->fullname` → `$profesor->full_name`: en cfla el accessor es
        `getFullNameAttribute()`, o sea `full_name` (el legacy imprimía vacío);
      · la fecha del legacy venía de una variable `$fecha` que la vista nunca
        definía (la tomaba del controller, pero a veces no llegaba) → aquí se
        usa la que pasa `format()`.
      · la columna "Aprendizaje a ser alcanzado" se rotula "Aprendizaje
        alcanzado": la columna y el `FormRequest` se llaman `aprendizaje_alcanzado`.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Evaluación - Formato 5</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-transform: uppercase; }
        h1, h2, h4 { text-align: center; color: #2c3e50; margin-bottom: 0.2rem; vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .format table { border: 1px solid #ccc; }
        .format th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; vertical-align: top; }
        .format th { background-color: #ccc; color: white; }
        td { font-size: 0.6rem; }
        .cabecera { margin-bottom: 20px; font-size: 0.8rem; border: 1px solid #ccc; }
        .cabecera p { margin: 5px 0; }
        .cabecera span { font-weight: bold; margin: 5px 0; }
        .page-break { page-break-after: always; page-break-inside: avoid; }
        .title { font-weight: bold; font-size: 14px; }
        .cierre { background-color: #ececec; }
        @media print { body { text-transform: uppercase; } }
    </style>
</head>
<body>

    @php
        $manager = $eievaluationk->manager;
        $peducativo = $eievaluationk->peducativo;
        $membreteTitulo = 'PLAN DE EVALUACIÓN';
        // Solo las áreas del docente y del lapso del plan: es lo que se evalúa
        // en ese periodo.
        $pevaluacions = $eievaluationk->getPevaluacions($profesor?->id, $eievaluationk->lapso_id);
    @endphp

    @forelse ($pevaluacions as $item)
        @php
            $posiciones = $eievaluationk->getPositionsForArea($item->id);
        @endphp

        @include('inicial.shared.membrete', ['titulo' => $membreteTitulo])

        <h4>Plan de Evaluación</h4>

        <table class="format" style="font-size:0.8rem;margin-bottom:0.2rem;padding-bottom:0.2rem;">
            <tr>
                <td><strong>Área de aprendizaje:</strong><br>
                    {{ $item->asignatura?->name ?? '—' }}
                </td>
                <td><strong>Docente:</strong><br>
                    {{ $profesor?->full_name ?? $item->profesor?->full_name ?? '—' }}
                </td>
                <td><strong>Grupo:</strong><br>
                    {{ $item->grado?->name ?? $eievaluationk->grado?->name ?? '—' }} ·
                    {{ $eievaluationk->seccion?->name ?? '—' }}
                </td>
            </tr>
        </table>

        <table class="format" style="font-size:0.8rem;margin-bottom:0.2rem;padding-bottom:0.2rem;">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nombre de los niños</th>
                    <th>Aprendizaje alcanzado</th>
                    <th>Indicadores</th>
                    <th>Instrumento</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posiciones as $position)
                    <tr>
                        <td>{{ $position->fecha?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $position->nombre_ninos ?? '—' }}</td>
                        <td>{!! nl2br(e($position->aprendizaje_alcanzado)) !!}</td>
                        <td>{!! nl2br(e($position->indicadores)) !!}</td>
                        <td>{!! nl2br(e($position->instrumento)) !!}</td>
                        <td>{!! nl2br(e($position->observacion)) !!}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No hay datos</td></tr>
                @endforelse

                <tr>
                    <td colspan="6" class="cierre">
                        Lapso: {{ $eievaluationk->lapso?->name ?? '—' }} ·
                        @if ($eievaluationk->pensum)
                            Área: {{ $eievaluationk->pensum->asignatura?->name ?? '—' }} ·
                        @endif
                        Tiempo de ejecución:
                        {{ $eievaluationk->finicial->format('d') }}
                        al
                        {{ $eievaluationk->ffinal->format('d-m-Y') }}
                    </td>
                </tr>
                <tr>
                    <td>Firma del Docente:</td>
                    <td colspan="4"></td>
                    <td>Fecha: {{ $fecha }}</td>
                </tr>
                <tr>
                    <td>Firma del Director:</td>
                    <td colspan="4"></td>
                    <td>Fecha:</td>
                </tr>
            </tbody>
        </table>

        @if (! $loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p style="font-size:0.8rem;">No hay áreas evaluables registradas en este plan.</p>
    @endforelse

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