{{--
    Vista de solo lectura del plan quincenal: cabecera + estrategias + resúmenes.
    Se alimenta de `openModal('view', $id)`.
--}}
<div class="space-y-6">

    @php
        // DOS consumidores, una sola vista:
        //   · el modal del docente, que le pasa el id y el parcial carga lo que
        //     necesita;
        //   · las perspectivas de revisión (F5), que le pasan `$plan` YA
        //     cargado con todas sus relaciones — así no se repite la consulta.
        $plan = $plan ?? \App\Models\app\Inicial\Eiplanningbwk::with(['grado', 'seccion', 'eiprojectk'])->find($eiplanningbwk_id);
    @endphp

    @if (! $plan)
        <p class="text-sm text-gray-500">Plan no encontrado.</p>
    @else
        {{-- ═══ Cabecera ═══ --}}
        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-xs font-medium text-gray-500">Grado · Sección</dt>
                <dd class="text-sm text-gray-200 mt-0.5">
                    {{ $plan->grado?->name ?? '—' }} · {{ $plan->seccion?->name ?? '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Período</dt>
                <dd class="text-sm text-gray-200 mt-0.5">
                    {{ $plan->finicial->format('d/m/Y') }}
                    →
                    {{ $plan->ffinal->format('d/m/Y') }}
                    <span class="text-gray-500">({{ $plan->tiempo_ejecucion }} {{ $plan->tiempo_ejecucion == 1 ? 'semana' : 'semanas' }})</span>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Proyecto vinculado</dt>
                <dd class="text-sm text-gray-200 mt-0.5">
                    {{ $plan->eiprojectk ? \Illuminate\Support\Str::limit($plan->eiprojectk->diagnostico, 60) : '—' }}
                </dd>
            </div>
        </dl>

        <div>
            <dt class="text-xs font-medium text-gray-500">Diagnóstico inicial</dt>
            <dd class="text-sm text-gray-300 mt-1 whitespace-pre-line">{{ $plan->diagnostico ?: '—' }}</dd>
        </div>

        @if ($plan->observacion)
            <div>
                <dt class="text-xs font-medium text-gray-500">Observación</dt>
                <dd class="text-sm text-gray-300 mt-1 whitespace-pre-line">{{ $plan->observacion }}</dd>
            </div>
        @endif

        {{-- ═══ Estrategias agrupadas por momento ═══ --}}
        @php $ordenadas = $plan->getOrderedStrategies(); @endphp

        <div>
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">
                Estrategias ({{ $ordenadas->count() }} de 50)
            </h4>

            @forelse ($ordenadas as $strategy)
                <div class="rounded-lg border border-white/10 bg-gray-800/40 px-4 py-3 mb-2">
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <p class="text-xs font-medium text-amber-300">{{ $strategy->momento_rutina_diaria }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-gray-400">
                                {{ \App\Models\app\Inicial\Eiplanningbwstrategy::WEEK_DAYS[$strategy->day_of_week] ?? $strategy->day_of_week }}
                            </span>
                            @if ($strategy->order)
                                <span class="text-[10px] text-gray-500 rounded px-1.5 py-0.5 bg-white/5 border border-white/5">
                                    #{{ $strategy->order }}
                                </span>
                            @endif
                        </div>
                    </div>
                    {{-- nl2br(e()) en lugar de as_replace() con el echo crudo de Blade (XSS del legacy). --}}
                    <p class="text-sm text-gray-300">{!! nl2br(e($strategy->estrategia)) !!}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                    Este plan aún no tiene estrategias.
                </p>
            @endforelse
        </div>

        {{-- ═══ Resúmenes ═══ --}}
        <div>
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Resúmenes por área</h4>

            @php $resumenes = $plan->getOrderedSummaries(); @endphp

            @forelse ($resumenes as $resumen)
                <div class="rounded-lg border border-white/10 bg-gray-800/40 px-4 py-3 mb-2 space-y-1.5">
                    <p class="text-sm font-medium text-purple-300">
                        {{ $resumen->pevaluacion?->pensum?->asignatura?->name ?? 'Área' }}
                        @if ($resumen->componente)
                            <span class="text-gray-400 font-normal">· {{ $resumen->componente }}</span>
                        @endif
                    </p>
                    @foreach (['objetivo' => 'Objetivo', 'aprendizaje_esperado' => 'Aprendizaje esperado', 'indicadores' => 'Indicadores'] as $campo => $etiqueta)
                        @if ($resumen->{$campo})
                            <p class="text-xs text-gray-400"><span class="text-gray-500">{{ $etiqueta }}:</span>
                                {!! nl2br(e($resumen->{$campo})) !!}
                            </p>
                        @endif
                    @endforeach
                </div>
            @empty
                <p class="text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                    Este plan aún no tiene resúmenes.
                </p>
            @endforelse
        </div>
    @endif
</div>
