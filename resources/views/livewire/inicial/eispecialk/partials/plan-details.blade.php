{{--
    Vista de solo lectura del plan especial: cabecera + estrategias + actividades.
    Se alimenta de `openModal('view', $id)`.
--}}
<div class="space-y-6">

    @php
        // DOS consumidores, una sola vista:
        //   · el modal del docente, que le pasa el id y el parcial carga lo que
        //     necesita;
        //   · las perspectivas de revisión (F5), que le pasan `$plan` YA
        //     cargado con todas sus relaciones — así no se repite la consulta.
        $plan = $plan ?? \App\Models\app\Inicial\Eispecialk::with(['grado', 'seccion', 'pensum.asignatura', 'pensum.grado'])->find($eispecialk_id);
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
        </dl>

        @if ($plan->pensum)
            <div>
                <dt class="text-xs font-medium text-gray-500">Área de aprendizaje</dt>
                <dd class="text-sm text-gray-200 mt-0.5">
                    {{ $plan->pensum->asignatura?->name ?? '—' }} · {{ $plan->pensum->grado?->name ?? '—' }}
                </dd>
            </div>
        @endif

        <div>
            <dt class="text-xs font-medium text-gray-500">Justificación del plan</dt>
            <dd class="text-sm text-gray-300 mt-1 whitespace-pre-line">{{ $plan->justificacion ?: '—' }}</dd>
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
                                {{ \App\Models\app\Inicial\Eispecialstrategy::WEEK_DAYS[$strategy->day_of_week] ?? $strategy->day_of_week }}
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

        {{-- ═══ Actividades ═══ --}}
        <div>
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Actividades por área</h4>

            @php $actividades = $plan->getOrderedActivities(); @endphp

            @forelse ($actividades as $actividad)
                <div class="rounded-lg border border-white/10 bg-gray-800/40 px-4 py-3 mb-2 space-y-1.5">
                    <p class="text-sm font-medium text-purple-300">
                        {{ $actividad->pevaluacion?->pensum?->asignatura?->name ?? 'Área' }}
                        @if ($actividad->componente)
                            <span class="text-gray-400 font-normal">· {{ $actividad->componente }}</span>
                        @endif
                    </p>
                    @foreach (['objetivo' => 'Objetivo', 'aprendizaje_esperado' => 'Aprendizaje esperado', 'indicadores' => 'Indicadores'] as $campo => $etiqueta)
                        @if ($actividad->{$campo})
                            <p class="text-xs text-gray-400"><span class="text-gray-500">{{ $etiqueta }}:</span>
                                {!! nl2br(e($actividad->{$campo})) !!}
                            </p>
                        @endif
                    @endforeach
                </div>
            @empty
                <p class="text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                    Este plan aún no tiene actividades.
                </p>
            @endforelse
        </div>
    @endif
</div>
