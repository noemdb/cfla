<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Inscripciones</title>
    <style>
        /* No usar `* { margin:0 }` ni `html { margin:0 }`: dompdf lo aplica al
           contexto de página y anula el margen de @page. */
        * { box-sizing: border-box; }
        body, h1, p, table, div { margin: 0; padding: 0; }
        @page { margin: 2cm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 7.5pt; color: #111827; line-height: 1.15; }

        .head { text-align: center; border-bottom: 1px solid #9ca3af; padding-bottom: 2px; margin-bottom: 3px; }
        .head .inst { font-size: 11pt; font-weight: 800; letter-spacing: .3px; }
        .head .legal { font-size: 6.5pt; color: #374151; }
        .head .addr { font-size: 6pt; color: #6b7280; }

        h1 { font-size: 9pt; font-weight: 800; text-align: center; text-transform: uppercase; margin: 3px 0 1px; }
        .sub { text-align: center; font-size: 7pt; color: #374151; margin-bottom: 3px; }

        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        table.meta td { border: 0; padding: 0; font-size: 6.5pt; color: #4b5563; }
        table.meta td.r { text-align: right; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: .5px solid #9ca3af; padding: 1px 3px; font-size: 6.8pt; vertical-align: middle; }
        table.data th { background: #e5e7eb; font-weight: 700; text-transform: uppercase; font-size: 6pt; letter-spacing: .2px; text-align: left; }
        table.data tbody tr:nth-child(even) td { background: #f8f8f8; }
        .c { text-align: center; }
        .num { width: 16px; }
        .ci { width: 60px; white-space: nowrap; }
        .sex { width: 34px; }
        .fnac { width: 56px; white-space: nowrap; }
        .empty { text-align: center; color: #9ca3af; padding: 8px; }

        table.foot { width: 100%; border-collapse: collapse; margin-top: 4px; border-top: 1px solid #d1d5db; }
        table.foot td { border: 0; padding: 2px 0 0; font-size: 6pt; color: #6b7280; }
        table.foot td.r { text-align: right; }
    </style>
</head>
<body>

    <div class="head">
        <div class="inst">{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</div>
        @if($institucion?->legalname || $institucion?->rif_institution)
            <div class="legal">
                {{ $institucion?->legalname }}
                @if($institucion?->rif_institution) · RIF: {{ $institucion->rif_institution }} @endif
            </div>
        @endif
        @php
            $direccion = collect([$institucion?->address, $institucion?->city, $institucion?->state])
                ->filter()->implode(' · ');
        @endphp
        @if($direccion || $institucion?->phone)
            <div class="addr">
                {{ $direccion }}@if($direccion && $institucion?->phone) · @endif
                @if($institucion?->phone) Telf: {{ $institucion->phone }} @endif
            </div>
        @endif
    </div>

    <h1>Listado de Inscripciones</h1>
    <div class="sub">
        {{ $pestudio?->name ?? '' }}@if($pestudio?->name) · @endif{{ $grado?->name ?? '' }} — Sección {{ $seccion->name ?? '' }}
    </div>

    <table class="meta">
        <tr>
            <td>Total de estudiantes: <strong>{{ $total }}</strong></td>
            <td class="r">Fecha: {{ $fecha }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="num c">N°</th>
                <th class="ci">Cédula</th>
                <th>Apellidos y Nombres</th>
                <th class="sex c">Sexo</th>
                <th class="fnac c">F. Nac.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inscripcions as $i => $inscripcion)
                @php $est = $inscripcion->estudiant; @endphp
                <tr>
                    <td class="num c">{{ $i + 1 }}</td>
                    <td class="ci">{{ $est?->ci_estudiant ?? '—' }}</td>
                    <td>{{ trim(($est?->lastname ?? '').' '.($est?->name ?? '')) ?: '—' }}</td>
                    <td class="sex c">{{ $est?->gender ? strtoupper(substr($est->gender, 0, 1)) : '—' }}</td>
                    <td class="fnac c">
                        @if($est?->date_birth && $est->date_birth !== '0000-00-00')
                            {{ \Carbon\Carbon::parse($est->date_birth)->format('d/m/Y') }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">No hay estudiantes inscritos en esta sección.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="foot">
        <tr>
            <td>Elaborado por: {{ Auth::user()?->username ?? 'Sistema' }}</td>
            <td class="r">{{ $institucion?->name ?? 'SAEFL' }} · {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

</body>
</html>
