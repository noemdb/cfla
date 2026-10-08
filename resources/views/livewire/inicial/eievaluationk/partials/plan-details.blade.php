{{--
    Vista de solo lectura del plan de evaluación: cabecera + posiciones por área.
    Se alimenta de `openModal('view', $id)`.

    No hay estrategias: el contenido del plan son las posiciones, agrupadas por
    área de aprendizaje para que la revisión sea legible.
--}}
<div class="space-y-6">

    @php
        // DOS consumidores, una sola vista:
        //   · el modal del docente, que le pasa el id y el parcial carga lo que
        //     necesita;
        //   · las perspectivas de revisión (F5), que le pasan `$plan` YA
        //     cargado con todas sus relaciones — así no se repite la consulta.
        $plan = $plan ?? \App\Models\app\Inicial\Eievaluationk::with(['grado', 'seccion', 'lapso'])->find($eievaluationk_id);
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
                {{-- El lapso es obligatorio en este documento: se muestra siempre. --}}
                <dt class="text-xs font-medium text-gray-500">Lapso</dt>
                <dd class="text-sm text-gray-200 mt-0.5">{{ $plan->lapso?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Período</dt>
                <dd class="text-sm text-gray-200 mt-0.5">
                    {{ $plan->finicial->format('d/m/Y') }}
                    →
                    {{ $plan->ffinal->format('d/m/Y') }}
                </dd>
            </div>
        </dl>

        <div>
            <dt class="text-xs font-medium text-gray-500">Observaciones</dt>
            <dd class="text-sm text-gray-300 mt-1 whitespace-pre-line">{{ $plan->observaciones ?: '—' }}</dd>
        </div>

        @if ($plan->asistencia)
            <div>
                <dt class="text-xs font-medium text-gray-500">Asistencia</dt>
                <dd class="text-sm text-gray-300 mt-1 whitespace-pre-line">{{ $plan->asistencia }}</dd>
            </div>
        @endif

        @if ($plan->recomendacion)
            <div>
                <dt class="text-xs font-medium text-gray-500">Recomendación</dt>
                <dd class="text-sm text-gray-300 mt-1 whitespace-pre-line">{{ $plan->recomendacion }}</dd>
            </div>
        @endif

        @if ($plan->observacion)
            <div>
                <dt class="text-xs font-medium text-gray-500">Observación adicional</dt>
                <dd class="text-sm text-gray-300 mt-1 whitespace-pre-line">{{ $plan->observacion }}</dd>
            </div>
        @endif

        {{-- ═══ Posiciones agrupadas por área ═══ --}}
        @php $ordenadas = $plan->getOrderedEvaluationps(); @endphp

        <div>
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">
                Posiciones ({{ $ordenadas->count() }})
            </h4>

            @forelse ($ordenadas->groupBy('pevaluacion_id') as $delArea)
                <div class="mb-3">
                    <p class="text-xs text-purple-300 font-medium mb-1">
                        {{ $delArea->first()->pevaluacion?->pensum?->asignatura?->name ?? 'Área sin nombre' }}
                    </p>

                    @foreach ($delArea as $position)
                        <div class="rounded-lg border border-white/10 bg-gray-800/40 px-4 py-3 mb-2 space-y-1.5">
                            <p class="text-sm text-gray-200">
                                @if ($position->nombre_ninos)
                                    {{ $position->nombre_ninos }}
                                @else
                                    <span class="text-gray-500 italic">Participantes sin nombrar</span>
                                @endif
                                @if ($position->fecha)
                                    <span class="text-xs text-gray-500">
                                        · {{ $position->fecha->format('d/m/Y') }}
                                    </span>
                                @endif
                            </p>

                            @foreach ([
                                'aprendizaje_alcanzado' => 'Aprendizaje alcanzado',
                                'componente' => 'Componente',
                                'indicadores' => 'Indicadores',
                                'instrumento' => 'Instrumento',
                                'observacion' => 'Observación',
                            ] as $campo => $etiqueta)
                                @if ($position->{$campo})
                                    <p class="text-xs text-gray-400">
                                        <span class="text-gray-500">{{ $etiqueta }}:</span>
                                        {!! nl2br(e($position->{$campo})) !!}
                                    </p>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @empty
                <p class="text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                    Este plan aún no tiene posiciones registradas.
                </p>
            @endforelse
        </div>
    @endif
</div>