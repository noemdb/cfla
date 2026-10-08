{{--
    Wizard de estrategias: rejilla de 5 días × 10 momentos = 50 celdas.

    ─────────────────────────────────────────────────────────────────
    PARCIAL COMPARTIDO entre `eiplanningwk` (semanal) y `eiplanningbwk`
    (quincenal)
    ─────────────────────────────────────────────────────────────────
    Los dos documentos tienen EXACTAMENTE la misma rejilla, y ambos
    componentes exponen los mismos nombres (`strategies`, `weekDays`,
    `activeDay`, `activeMomentIndex`, `momentLabels`, `planEstrategias`) y las
    mismas acciones (`setActiveDay`, `setActiveMoment`, `previousMoment`,
    `nextMoment`, `saveCurrentStrategy`, `saveStrategies`,
    `confirmDeleteStrategy`, `deleteStrategy`). Por eso no lleva el nombre de
    ningún documento: copiarlo dos veces garantizaría que la quincenal se
    quedara atrás en la siguiente mejora de la semanal.

    Lo que SÍ es específico de cada documento (la cabecera, los resúmenes y el
    detalle) vive en partials propios, porque cambian de propiedad.

    Pestañas de día + pills de momento, en el ORDEN REAL de la rutina diaria
    (no alfabético). Al guardar una celda el foco avanza al momento siguiente,
    que es el flujo natural del docente: recorre la rutina como la vive.

    R4 del blueprint: la regla documentada de "≥3 días con contenido" NO bloquea
    el guardado. `getDayProgress()` la muestra como advertencia; basta UNA celda
    con texto.

    El texto se escribe en `strategies[día][momento].estrategia`, que el
    componente traduce a la columna `lunes` del modelo (quirk D3).
--}}
<div class="space-y-4">

    {{-- ═══ Resumen del wizard ═══ --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg bg-white/5 border border-white/10 px-4 py-3">
        <div class="text-xs text-gray-400">
            <span class="text-gray-200 font-medium">{{ $planEstrategias }}</span> de 50 celdas guardadas
        </div>

        @php
            $diasConContenido = count($this->getDaysWithContent());
        @endphp
        @if ($diasConContenido < 3)
            {{-- Advertencia, no error: R4 se descartó como validación dura. --}}
            <p class="text-xs text-amber-400/90">
                Solo {{ $diasConContenido }} {{ $diasConContenido == 1 ? 'día tiene' : 'días tienen' }} contenido
                (se recomiendan 3). No bloquea el guardado.
            </p>
        @else
            <p class="text-xs text-emerald-400/90">
                {{ $diasConContenido }} días con contenido
            </p>
        @endif
    </div>

    {{-- ═══ Pestañas de día con progreso ═══ --}}
    <div class="flex flex-wrap gap-1.5 border-b border-white/10 pb-2" role="tablist" aria-label="Días de la semana">
        @foreach ($weekDays as $clave => $nombre)
            @php $p = $this->getDayProgress($clave); @endphp
            <button wire:click="setActiveDay('{{ $clave }}')" type="button" role="tab"
                @if ($activeDay === $clave) aria-selected="true" @else aria-selected="false" @endif
                class="px-3 py-2 rounded-lg text-xs font-medium transition-colors border
                       {{ $activeDay === $clave
                            ? 'bg-cyan-500/20 text-cyan-200 border-cyan-500/40'
                            : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10' }}">
                {{ $nombre }}
                <span class="ml-1 text-[10px] {{ $p['completed'] > 0 ? 'text-emerald-400' : 'text-gray-500' }}">
                    {{ $p['completed'] }}/{{ $p['total'] }}
                </span>
            </button>
        @endforeach
    </div>

    {{-- ═══ Pills de momento, en orden de rutina ═══ --}}
    {{-- Se itera por ÍNDICE: los nombres de momento ("Periodo: Planificación")
         tienen espacios y dos puntos, que romperían wire:model y los clicks.
         `$momentLabels` viene de render() reindexado 0..9. --}}
    <div class="flex flex-wrap gap-1.5">
        @foreach ($momentLabels as $i => $nombre)
            @php
                $celda = $strategies[$activeDay][$i] ?? null;
                $tiene = $celda && trim((string) ($celda['estrategia'] ?? '')) !== '';
            @endphp
            <button wire:click="setActiveMoment({{ $i }})" type="button"
                @class([
                    'px-2.5 py-1 rounded-full text-[11px] transition-colors border',
                    'bg-amber-500/20 text-amber-200 border-amber-500/40' => $activeMomentIndex === $i,
                    'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10' => $activeMomentIndex !== $i,
                ])
                @if ($activeMomentIndex === $i) aria-current="true" @endif>
                {{ $nombre }}
                @if ($tiene)
                    <span class="text-emerald-400" aria-label="con contenido">●</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ═══ Navegación entre momentos ═══ --}}
    <div class="flex items-center justify-between gap-3">
        <button wire:click="previousMoment" type="button"
            @disabled($activeMoment === array_key_first($moments))
            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs text-gray-300 bg-white/5 hover:bg-white/10 border border-white/10 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Anterior
        </button>

        <p class="text-xs text-gray-400">
            {{ $weekDays[$activeDay] }} · <span class="text-amber-300">{{ $activeMoment }}</span>
        </p>

        <button wire:click="nextMoment" type="button"
            @disabled($activeMoment === array_key_last($moments))
            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs text-gray-300 bg-white/5 hover:bg-white/10 border border-white/10 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
            Siguiente
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    {{-- ═══ Celda activa ═══ --}}
    @php
        $celda = $strategies[$activeDay][$activeMomentIndex] ?? ['id' => null, 'estrategia' => '', 'order' => null];
        $rutaCelda = "strategies.{$activeDay}.{$activeMomentIndex}";
    @endphp

    <div class="rounded-lg border border-white/10 bg-gray-800/40 p-4 space-y-3">
        <label for="celda-estrategia" class="block text-xs font-medium text-gray-400">
            Estrategia · {{ $weekDays[$activeDay] }} · {{ $activeMoment }}
        </label>

        <textarea id="celda-estrategia" rows="4" wire:model.live.debounce.600ms="{{ $rutaCelda }}.estrategia"
            placeholder="Qué hace el niño en este momento de la rutina, con qué recursos, cómo se adapta al grupo…"
            class="w-full bg-gray-900/60 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>

        <div class="flex flex-wrap items-end justify-between gap-3">
            <div class="w-28">
                <label for="celda-order" class="block text-xs font-medium text-gray-400 mb-1">Orden</label>
                <input id="celda-order" type="number" min="1" step="1"
                    wire:model.live="{{ $rutaCelda }}.order"
                    class="w-full bg-gray-900/60 border border-white/10 text-gray-200 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none">
            </div>

            <div class="flex items-center gap-2">
                @if ($celda['id'])
                    <button wire:click="confirmDeleteStrategy('{{ $activeDay }}', {{ $activeMomentIndex }})" type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs text-red-300 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6" />
                        </svg>
                        Eliminar
                    </button>
                @endif

                <button wire:click="saveCurrentStrategy" type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 px-3 py-1.5 text-xs font-medium text-white transition-colors">
                    Guardar y continuar
                </button>
            </div>
        </div>
    </div>
</div>
