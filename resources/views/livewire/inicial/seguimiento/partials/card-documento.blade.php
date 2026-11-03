{{--
    Tarjeta genérica para quincenal, proyecto, especial, evaluación e informe
    final. Acciones: Ver, Ficha, Imprimir y Revisión (esta última solo si el
    documento tiene campo revisable).
--}}
@php
    $esFinal = $entidad === 'eifinalks';

    $nivel = $esFinal
        ? ($documento->pevaluacion?->seccion?->name ?? '—')
        : trim(($documento->grado?->name ?? '—').' · '.($documento->seccion?->name ?? '—'));

    $periodo = $esFinal
        ? ($documento->pevaluacion?->lapso?->name ?? '—')
        : (($documento->finicial?->format('d/m/Y') ?? '—').' → '.($documento->ffinal?->format('d/m/Y') ?? '—'));

    $docente = $esFinal
        ? $documento->pevaluacion?->profesor?->full_name
        : $documento->profesor?->full_name;

    $area = $esFinal
        ? $documento->pevaluacion?->pensum?->asignatura?->name
        : $documento->pensum?->asignatura?->name;

    [$contador, $etiquetaContador] = match ($entidad) {
        'eiplanningbwks' => [(int) ($documento->estrategias_count ?? 0), 'estrategias'],
        'eiprojectks' => [(int) ($documento->estrategias_count ?? 0), 'estrategias'],
        'eispecialks' => [(int) ($documento->actividades_count ?? 0), 'actividades'],
        'eievaluationks' => [(int) ($documento->posiciones_count ?? 0), 'posiciones'],
        'eifinalks' => [(int) ($documento->expectativas_count ?? 0), 'expectativas'],
        default => [0, 'ítems'],
    };

    $campoRevision = \App\Services\Inicial\RegistroInicial::campoRevision($entidad)
        ? 'revision_seguimiento'
        : null;
    $revisado = $campoRevision && filled($documento->{$campoRevision});
    $estadoActual = $estado['clave'];
    $requiereAtencion = $campoRevision
        && ($estadoActual === 'sin_iniciar' || ! $revisado);

    // Texto principal según el documento: evaluación y plan especial no usan
    // `diagnostico`, y mostrar «Sin diagnóstico» ocultaría su contenido real.
    $textoPrincipal = match ($entidad) {
        'eispecialks' => $documento->justificacion,
        'eievaluationks' => $documento->observaciones,
        default => $documento->diagnostico,
    };

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
                    {{ \App\Services\Inicial\RegistroInicial::titulo($entidad) }}
                    <span class="text-gray-500 font-normal">N° {{ $documento->id }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $nivel }}</p>
            </div>

            <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider rounded-md px-2 py-0.5 bg-cyan-500/15 text-cyan-300 border border-cyan-500/20">
                {{ $periodo }}
            </span>
        </div>

        <p class="mt-3 text-xs text-gray-400 truncate">
            Docente: <span class="text-gray-200">{{ $docente ?? '—' }}</span>
        </p>
        <p class="text-xs text-gray-500 truncate">
            Área: <span class="text-gray-400">{{ $area ?? '—' }}</span>
        </p>
        <p class="mt-1 text-[11px] text-gray-500">
            Actualizado: {{ $documento->updated_at?->format('d/m/Y H:i') ?? '—' }}
        </p>

        @if ($esFinal)
            <p class="mt-3 text-sm text-gray-300 line-clamp-3">
                {{ $documento->title ?: 'Sin título.' }}
            </p>
            <p class="mt-1 text-xs text-gray-500 truncate">
                Estudiante: <span class="text-gray-300">{{ $documento->expectant?->full_name ?? '—' }}</span>
            </p>
        @else
            <p class="mt-3 text-sm text-gray-300 line-clamp-3">
                {{ $textoPrincipal ?: 'Sin contenido registrado.' }}
            </p>
        @endif

        <p class="mt-4 text-[11px] uppercase tracking-wider text-gray-500">
            {{ $contador }} {{ $etiquetaContador }}
        </p>

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
