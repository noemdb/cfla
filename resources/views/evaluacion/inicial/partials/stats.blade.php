{{--
    Indicadores de planificación de Educación Inicial.

    Controlador: App\Http\Controllers\Evaluacion\InicialController::index
    Datos:       App\Services\Inicial\EducationStatsService

    ─────────────────────────────────────────────────────────────────────────────
    POR QUÉ NO HAY BADGES DE PORCENTAJE
    ─────────────────────────────────────────────────────────────────────────────
    El legacy pintaba tres tarjetas con badges FIJOS: +12 %, +5 y 67 %. No medían
    nada —los números estaba escritos en la vista— y además fingían una
    tendencia temporal que estos datos no tienen (son un conteo total, no una
    comparación con el mes pasado).

    Aquí cada tarjeta muestra el dato ABSOLUTO y, si algún día se configura
    `config('inicial.max_number.*')`, entonces sí una barra de cumplimiento. Hoy
    esos máximos son `null` porque las columnas `peducativos.max_number_*` no
    existen ni en el legacy ni aquí (decisión D6): no se inventa un techo para
    medir un porcentaje contra él.
--}}
@php
    $tarjetas = [
        ['titulo' => 'Documentos registrados', 'valor' => $stats['totalRecords'], 'tono' => 'cyan',
         'nota' => 'Suma de los seis documentos del módulo con los filtros activos.'],
        ['titulo' => 'Proyectos en curso', 'valor' => $stats['activeProjects'], 'tono' => 'amber',
         'nota' => 'Proyectos de aula con fecha de inicio y sin fecha de cierre.'],
        ['titulo' => 'Evaluaciones cerradas', 'valor' => $stats['completedEvaluations'], 'tono' => 'emerald',
         'nota' => 'Planes de evaluación con fecha de cierre.'],
    ];

    $tonos = [
        'cyan' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-200',
        'amber' => 'border-amber-500/30 bg-amber-500/10 text-amber-200',
        'emerald' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200',
    ];
@endphp

<section aria-labelledby="indicadores-titulo" class="space-y-4">
    <div class="flex items-baseline justify-between gap-3">
        <h2 id="indicadores-titulo"
            class="text-xs font-bold uppercase tracking-widest text-gray-500">
            Indicadores de planificación
        </h2>
        <span class="text-[11px] text-gray-600">
            Actualizado: {{ now()->format('H:i') }}
        </span>
    </div>

    {{-- Tarjetas principales --}}
    <div class="grid gap-3 sm:grid-cols-3">
        @foreach ($tarjetas as $tarjeta)
            <div @class([
                'rounded-xl border p-4',
                $tonos[$tarjeta['tono']] ?? $tonos['cyan'],
            ])>
                <p class="text-xs opacity-80">{{ $tarjeta['titulo'] }}</p>
                <p class="mt-1 text-3xl font-bold tabular-nums">{{ number_format($tarjeta['valor']) }}</p>
                <p class="mt-1 text-[11px] opacity-70">{{ $tarjeta['nota'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Detalle por documento --}}
    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($indicadores as $indicador)
            <div class="rounded-lg border border-white/10 bg-gray-900/60 p-3">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm text-gray-200">{{ $indicador['titulo'] }}</p>
                    <p class="text-sm font-semibold tabular-nums text-gray-100">
                        {{ number_format($indicador['conteo']) }}
                    </p>
                </div>

                @if ($indicador['porcentaje'] === null)
                    {{-- Sin máximo configurado: el conteo absoluto es todo el
                         dato que se puede afirmar. No se inventa un 100 %. --}}
                    <p class="mt-1 text-[11px] text-gray-600">
                        Sin máximo configurado: se muestra el conteo, no un porcentaje.
                    </p>
                @else
                    <div class="mt-2 h-1.5 rounded-full bg-white/10 overflow-hidden">
                        <div @class([
                                'h-full rounded-full transition-all',
                                'bg-emerald-500' => $indicador['porcentaje'] >= 100,
                                'bg-amber-500' => $indicador['porcentaje'] < 100,
                            ])
                            style="width: {{ $indicador['porcentaje'] }}%"
                            role="progressbar"
                            aria-valuenow="{{ $indicador['porcentaje'] }}"
                            aria-valuemin="0" aria-valuemax="100"
                            aria-label="{{ $indicador['titulo'] }}"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-500">
                        {{ $indicador['porcentaje'] }} % del máximo ({{ $indicador['maximo'] }})
                    </p>
                @endif
            </div>
        @endforeach
    </div>
</section>