{{--
    Tarjeta rica de planificación semanal.

    Mismo lenguaje visual que el listado del docente: Ver (modal), Ficha
    (detalle), Imprimir (formato) y Revisión (abre el modal en modo escritura).
--}}
@php
    $totalCeldas = count(config('inicial.dias_semana')) * count(config('inicial.momentos_rutina_diaria'));

    $escritas = (int) ($documento->estrategias_count ?? 0);
    $resumenes = (int) ($documento->resumenes_count ?? 0);
    $porcentaje = $totalCeldas > 0 ? min(100, round($escritas / $totalCeldas * 100)) : 0;

    $campoRevision = \App\Services\Inicial\RegistroInicial::campoRevision($entidad)
        ? 'revision_seguimiento'
        : null;
    $revisado = $campoRevision && filled($documento->{$campoRevision});
    $requiereAtencion = $estado['clave'] === 'sin_iniciar' || ! $revisado;

    $variante = [
        'emerald' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-300',
        'amber' => 'border-amber-500/20 bg-amber-500/10 text-amber-300',
        'cyan' => 'border-cyan-500/20 bg-cyan-500/10 text-cyan-300',
        'gray' => 'border-white/10 bg-white/5 text-gray-400',
    ][$estado['variante']] ?? 'border-white/10 bg-white/5 text-gray-400';
@endphp

<article @class([
        'rounded-xl border bg-gray-900/60 transition-colors flex flex-col',
        'border-amber-500/30 hover:border-amber-400/50' => $requiereAtencion,
        'border-white/10 hover:border-cyan-500/30' => ! $requiereAtencion,
    ])
    wire:key="doc-{{ $entidad }}-{{ $documento->id }}">

    <div class="p-4 flex-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-100 truncate">
                    {{ $documento->grado?->name ?? 'Sin nivel' }} · {{ $documento->seccion?->name ?? 'Sin sección' }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $documento->finicial?->format('d/m/Y') ?? '—' }}
                    →
                    {{ $documento->ffinal?->format('d/m/Y') ?? '—' }}
                </p>
            </div>

            <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider rounded-md px-2 py-0.5 bg-cyan-500/15 text-cyan-300 border border-cyan-500/20">
                {{ $documento->tiempo_ejecucion ?? 1 }} {{ ($documento->tiempo_ejecucion ?? 1) == 1 ? 'semana' : 'semanas' }}
            </span>
        </div>

        <p class="mt-3 text-xs text-gray-400 truncate">
            Docente: <span class="text-gray-200">{{ $documento->profesor?->full_name ?? '—' }}</span>
        </p>
        <p class="text-xs text-gray-500 truncate">
            Área: <span class="text-gray-400">{{ $documento->pensum?->asignatura?->name ?? '—' }}</span>
        </p>
        <p class="mt-1 text-[11px] text-gray-500">
            Actualizado: {{ $documento->updated_at?->format('d/m/Y H:i') ?? '—' }}
        </p>

        <p class="mt-3 text-sm text-gray-300 line-clamp-3">
            {{ $documento->diagnostico ?: 'Sin diagnóstico.' }}
        </p>

        {{-- Progreso de estrategias: 0..50 --}}
        <div class="mt-4">
            <div class="flex items-center justify-between text-[11px] text-gray-500 mb-1">
                <span>Estrategias</span>
                <span class="tabular-nums">{{ $escritas }} / {{ $totalCeldas }}</span>
            </div>
            <div class="h-1.5 rounded-full bg-white/5 overflow-hidden" role="progressbar"
                aria-valuenow="{{ $escritas }}" aria-valuemin="0" aria-valuemax="{{ $totalCeldas }}"
                aria-label="Estrategias escritas">
                <div class="h-full bg-cyan-500 rounded-full" style="width: {{ $porcentaje }}%"></div>
            </div>
            <p class="mt-1 text-[11px] text-gray-600 tabular-nums">
                {{ $resumenes }} {{ $resumenes === 1 ? 'resumen' : 'resúmenes' }} por área
            </p>
        </div>

        {{-- Estado + revisión --}}
        <div class="mt-3 flex flex-wrap gap-1.5">
            <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-medium {{ $variante }}"
                title="{{ $estado['detalle'] }}">
                {{ $estado['etiqueta'] }}
            </span>

            @if ($requiereAtencion)
                <span class="inline-flex items-center rounded-md border border-amber-500/30 bg-amber-500/10 px-1.5 py-0.5 text-[10px] font-medium text-amber-200">
                    Requiere atención
                </span>
            @endif

            @if ($campoRevision)
                <span @class([
                        'inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-medium',
                        'border-emerald-500/20 bg-emerald-500/10 text-emerald-300' => $revisado,
                        'border-amber-500/20 bg-amber-500/10 text-amber-300' => ! $revisado,
                    ])>
                    {{ $revisado ? 'Revisado' : 'Sin revisión' }}
                </span>
            @endif
        </div>
    </div>

    <footer class="flex items-center gap-2 border-t border-white/5 px-4 py-2.5">
        <button type="button" wire:click="abrirDetalle({{ $documento->id }})"
            class="inline-flex items-center gap-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 px-2.5 py-1.5 text-xs font-medium text-gray-200 transition-colors">
            Ver
        </button>

        <a href="{{ route('inicials.seguimiento.'.$entidad.'.show', $documento->id) }}" wire:navigate
            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-cyan-300 hover:text-cyan-200 hover:bg-cyan-500/10 transition-colors">
            Ficha
        </a>

        <a href="{{ route('inicials.seguimiento.'.$entidad.'.format', $documento->id) }}" target="_blank" rel="noopener"
            class="ml-auto inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-gray-400 hover:text-gray-200 hover:bg-white/5 transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z" />
            </svg>
            Imprimir
        </a>

        @if ($campoRevision)
            <button type="button" wire:click="revisarDetalle({{ $documento->id }})"
                title="{{ $revisado ? 'Editar revisión' : 'Registrar revisión' }}"
                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-amber-300 hover:text-amber-200 hover:bg-amber-500/10 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Revisión
            </button>
        @endif
    </footer>
</article>
