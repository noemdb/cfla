<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Horarios de docentes · {{ $lapso?->name ?? 'Lapso' }}</title>
    @php($orientation = ($orientation ?? 'portrait') === 'landscape' ? 'landscape' : 'portrait')
    @php($perPage = max(1, min(6, (int) ($perPage ?? 2))))
    {{-- Cada hoja agrupa hasta $perPage docentes y fuerza salto de página, así
         se muestran TODOS los horarios sin recortar el contenido. --}}
    @php($sheets = collect($teachersData)->chunk($perPage))
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f3f4f6;color:#1f2937;min-height:100vh;}
        .wrap{max-width:1100px;margin:0 auto;padding:24px 16px;}
        header{background:#0d9488;color:#fff;border-radius:12px;padding:20px 24px;margin-bottom:20px;}
        header h1{font-size:1.4rem;font-weight:800;letter-spacing:0.2px;}
        header p{font-size:0.9rem;opacity:0.9;margin-top:4px;}
        .pestudio{border-left:4px solid #0d9488;padding:8px 12px;margin:28px 0 12px;background:#ecfdf5;border-radius:8px;}
        .pestudio h2{font-size:1.05rem;font-weight:800;color:#0f766e;}
        .pestudio p{font-size:0.8rem;color:#64748b;margin-top:2px;}
        .peducativo-block + .peducativo-block{border-top:3px solid #0f766e;margin-top:14px;}
        .peducativo-title{padding:8px 16px;font-size:0.82rem;font-weight:800;color:#fff;background:#0f766e;text-transform:uppercase;letter-spacing:0.04em;}
        .card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:18px;}
        .card-head{background:#f9fafb;border-bottom:1px solid #e5e7eb;padding:12px 16px;}
        .card-head h3{font-size:1rem;font-weight:800;color:#111827;}
        .card-head p{font-size:0.75rem;color:#6b7280;margin-top:2px;}
        .shift-title{padding:10px 16px;font-size:0.8rem;font-weight:800;color:#0d9488;text-transform:uppercase;letter-spacing:0.05em;background:#f0fdf4;border-bottom:1px solid #e5e7eb;}
        /* table-layout:fixed + ancho fijo de la 1ª columna: las 5 columnas de
           días se reparten todo el ancho disponible por igual. */
        table{width:100%;border-collapse:collapse;table-layout:fixed;}
        th{background:#f9fafb;color:#374151;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:10px 8px;border-bottom:1px solid #94a3b8;text-align:center;}
        th:first-child{width:84px;}
        th:not(:first-child){text-align:left;}
        td{border-bottom:1px solid #94a3b8;padding:8px;vertical-align:top;font-size:0.85rem;overflow-wrap:break-word;}
        td.time{text-align:center;font-weight:700;color:#374151;white-space:nowrap;width:84px;background:#f0fdf4;}
        td.break{background:#fef3c7;color:#b45309;text-align:left;font-weight:800;font-size:0.8rem;}
        .subject{font-weight:700;color:#0d9488;font-size:0.82rem;}
        .group{color:#0f766e;font-size:0.72rem;font-weight:700;margin-top:1px;}
        .meta{color:#6b7280;font-size:0.72rem;margin-top:2px;}
        .empty{color:#9ca3af;text-align:center;padding:8px;}
        footer{text-align:center;font-size:0.75rem;color:#6b7280;margin-top:24px;}
        @media print{
            @page{size:letter {{ $orientation }};margin:8mm;}
            html,body{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
            .wrap{max-width:none;padding:0;}
            /* Se conserva la estética (esquinas redondeadas, paleta teal y
               jerarquía tipográfica), sólo se compacta el ritmo vertical. */
            header{border-radius:8px;padding:9px 14px;margin-bottom:8px;}
            header h1{font-size:1.2rem;}
            header p{font-size:0.78rem;margin-top:2px;}
            .pestudio{margin:8px 0 5px;padding:5px 10px;break-after:avoid;page-break-after:avoid;}
            .pestudio h2{font-size:0.82rem;}
            .pestudio p{font-size:0.62rem;margin-top:1px;}
            .peducativo-block + .peducativo-block{border-top:2px solid #0f766e;margin-top:4px;}
            .peducativo-title{padding:2px 10px;font-size:0.58rem;}
            /* {{ $perPage }} docente(s) por hoja: cada hoja ocupa una página
               completa y las tarjetas conservan su alto natural (sin recortes). */
            .sheet{break-after:page;page-break-after:always;}
            .sheet:last-child{break-after:auto;page-break-after:auto;}
            .card{margin-bottom:3mm;border-radius:10px;box-shadow:none;border:1px solid #e5e7eb;break-inside:avoid;page-break-inside:avoid;}
            .card-head{padding:4px 10px;break-after:avoid;page-break-after:avoid;}
            .card-head h3{font-size:0.85rem;}
            .card-head p{font-size:0.68rem;margin-top:1px;}
            .shift-title{padding:2px 10px;font-size:0.66rem;break-after:avoid;page-break-after:avoid;}
            table{width:100%;border-collapse:collapse;table-layout:fixed;}
            th{padding:2px 4px;font-size:0.64rem;}
            th:first-child{width:72px;}
            td{padding:2px 4px;font-size:0.8rem;line-height:1.15;overflow:hidden;}
            td.time{width:72px;font-size:0.68rem;line-height:1.15;}
            td.break{font-size:0.66rem;padding:2px 4px;}
            .subject{font-size:0.8rem;line-height:1.15;}
            .group{font-size:0.6rem;margin-top:0;line-height:1.15;}
            .meta{font-size:0.6rem;margin-top:0;line-height:1.15;}
            .empty{padding:1px;line-height:1;}
            footer{font-size:0.68rem;margin-top:8px;}
        }
    </style>
</head>
<body>
    <div class="wrap">
        <header>
            <h1>Horarios de docentes</h1>
            <p>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }} · {{ $lapso?->name ?? '' }} · {{ $fecha }}</p>
            @if (! empty($area ?? null))
                <p style="margin-top:6px;font-size:0.8rem;opacity:0.9;">Área: {{ $area->name }} @if($area->code)[{{ $area->code }}]@endif · {{ $area->pestudio?->name ?? 'Sin P.Estudio' }} @if($area->peducativo?->name)· {{ $area->peducativo->name }}@endif</p>
            @endif
        </header>

        @forelse ($sheets as $sheet)
            <div class="sheet">
            @foreach ($sheet as $teacherData)
            @php($profesor = $teacherData['profesor'])
            <div class="card">
                <div class="card-head">
                    <h3>{{ $profesor->lastname }}, {{ $profesor->name }} · CI {{ $profesor->ci_profesor ?? '—' }}</h3>
                </div>

                {{-- Horarios del docente por P.Educativo, en un único horario
                     (los P.Estudios que lo componen ya vienen fusionados). --}}
                @foreach ($teacherData['peducativos'] as $peducativoData)
                    <div class="peducativo-block">
                        <div class="peducativo-title">{{ $peducativoData['peducativo']?->name ?? 'P.Educativo' }}</div>

                        @foreach ($peducativoData['schedules'] as $shiftSchedule)
                            <div class="shift-title">
                                Turno: {{ $shiftSchedule['shift']->name ?? ('Turno '.$shiftSchedule['shift']->id) }}
                                @if ($shiftSchedule['shift']->start_time)
                                    ({{ substr((string) $shiftSchedule['shift']->start_time, 0, 5) }}–{{ substr((string) $shiftSchedule['shift']->end_time, 0, 5) }})
                                @endif
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Lun</th>
                                        <th>Mar</th>
                                        <th>Mié</th>
                                        <th>Jue</th>
                                        <th>Vie</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($nBloque = 0)
                                    @foreach ($shiftSchedule['periods'] as $order => $dayPeriods)
                                        @php($firstPeriod = $dayPeriods->first())
                                        @php($esReceso = $dayPeriods->contains('is_break', true))
                                        @if(!$esReceso)
                                            @php($nBloque = $nBloque + 1)
                                        @endif
                                        <tr>
                                            <td class="time">
                                                @if($esReceso)
                                                    <span class="meta">{{ substr((string) $firstPeriod?->start_time, 0, 5) }}–{{ substr((string) $firstPeriod?->end_time, 0, 5) }}</span>
                                                @else
                                                    {{ $nBloque }}º<br>
                                                    <span class="meta">{{ substr((string) $firstPeriod?->start_time, 0, 5) }}–{{ substr((string) $firstPeriod?->end_time, 0, 5) }}</span>
                                                @endif
                                            </td>
                                            @foreach (range(1, 5) as $day)
                                                @php($period = $dayPeriods->get($day))
                                                @if ($period?->is_break)
                                                    <td class="break">Receso</td>
                                                @else
                                                    <td>
                                                        @include('timetable.partials.half-cell', [
                                                            'slots' => $shiftSchedule['grid']->get($order, collect())->get($day, collect()),
                                                            'emptyClass' => 'empty',
                                                            'lines' => function ($slot): array {
                                                                $pev = $slot->lesson?->pevaluacion;
                                                                $grado = (string) ($pev?->seccion?->grado?->code_sm ?? $pev?->seccion?->grado?->code ?? $pev?->seccion?->grado?->name ?? '');
                                                                $group = $pev?->grupo_estable_id ? (string) ($pev?->grupoEstable?->name ?? 'G'.$pev->grupo_estable_id) : '';
                                                                $meta = trim(trim($grado.' '.(string) ($pev?->seccion?->name ?? ''))
                                                                    .($slot->room?->code ? ' · Aula '.$slot->room->code : ''));

                                                                $lines = [
                                                                    ['class' => 'subject', 'text' => (string) ($pev?->pensum?->asignatura?->name ?? '?')],
                                                                ];
                                                                if ($group !== '') {
                                                                    $lines[] = ['class' => 'group', 'text' => $group];
                                                                }
                                                                $lines[] = ['class' => 'meta', 'text' => $meta];

                                                                return $lines;
                                                            },
                                                        ])
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                @endforeach
            </div>
            @endforeach
            </div>
        @empty
            <div class="card"><div class="empty">No hay docentes con asignaciones en los calendarios activos del lapso vigente.</div></div>
        @endforelse

        <footer>Horarios de docentes · {{ $institucion?->name ?? '' }} · Generado el {{ $fecha }}</footer>
    </div>
    @if (! empty($autoPrint))
        <script>window.addEventListener('load', function () { window.print(); });</script>
    @endif
</body>
</html>
