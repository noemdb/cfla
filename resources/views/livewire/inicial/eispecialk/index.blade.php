{{--
    CRUD de Plan Especial de Educación Inicial (entidad P0).

    Componente: App\Livewire\Inicial\EispecialkComponent
    Blueprint:  blueprint/inicial · fase F2

    Bloques (anatomía del doc 04 §2):
      1. Encabezado + filtros
      2. Listado en tarjetas
      3. Modal único ($modalType)
    El wizard de estrategias y el gestor de actividades van en un include para
    no dejar un archivo de 1.000 líneas.
--}}
<div class="space-y-6">

    {{-- ═══════════════════════════════════════════════════════════
         BLOQUE 1 · ENCABEZADO Y FILTROS
         ═══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-100">Plan Especial</h2>
            <p class="text-sm text-gray-400 mt-1">
                Plan justificado por una razón pedagógica, con actividades por área y estrategias día × momento.
            </p>
        </div>

        <div class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-56">
                <label for="f-search" class="block text-xs font-medium text-gray-400 mb-1">Buscar</label>
                <input id="f-search" type="search" wire:model.live.debounce.400ms="search"
                    placeholder="Diagnóstico u observación"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            </div>

            <div class="w-full sm:w-44">
                <label for="f-grado" class="block text-xs font-medium text-gray-400 mb-1">Grado</label>
                <select id="f-grado" wire:model.live="filterGrado"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                    <option value="">Todos</option>
                    @foreach ($listGrado as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-40">
                <label for="f-seccion" class="block text-xs font-medium text-gray-400 mb-1">Sección</label>
                <select id="f-seccion" wire:model.live="filterSeccion" @disabled(! $filterGrado)
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none disabled:opacity-50">
                    <option value="">Todas</option>
                    @foreach ($listSeccion as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-44">
                <label for="f-pensum" class="block text-xs font-medium text-gray-400 mb-1">Área</label>
                <select id="f-pensum" wire:model.live="filterPensum"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                    <option value="">Todas</option>
                    @foreach ($listPensumFiltro as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="openModal('create')" type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo plan especial
            </button>

            <button wire:click="openImport" type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 transition-colors"
                title="Traer planes del período anterior (s2526) como punto de partida">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Importar de s2526
            </button>

            <button wire:click="toggleView" type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 px-3 py-2 text-sm font-medium text-gray-300 transition-colors"
                title="{{ $viewMode === 'grid' ? 'Ver como tabla' : 'Ver como tarjetas' }}">
                @if ($viewMode === 'grid')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M3 6h18M3 18h18" />
                    </svg>
                    Tabla
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Tarjetas
                @endif
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         BLOQUE 2 · LISTADO EN TARJETAS
         ═══════════════════════════════════════════════════════════ --}}
    @if ($eispecialks->isEmpty())
        <div class="rounded-xl border border-dashed border-white/10 py-16 text-center">
            <p class="text-sm text-gray-400">Todavía no hay planes especiales.</p>
            <button wire:click="openModal('create')" class="mt-3 text-sm text-cyan-400 hover:text-cyan-300">
                Crear la primera
            </button>
        </div>
    @else
        @if ($viewMode === 'grid')
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($eispecialks as $plan)
                @php
                    $total = 50;
                    $escritas = $plan->eispecialstrategies()->count();
                    $actividades = $plan->activities()->count();
                @endphp
                <article
                    class="rounded-xl border border-white/10 bg-gray-900/60 hover:border-cyan-500/30 transition-colors flex flex-col">
                    <div class="p-4 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-100 truncate">
                                    {{ $plan->grado?->name ?? 'Sin grado' }} · {{ $plan->seccion?->name ?? 'Sin sección' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $plan->finicial->format('d/m/Y') }}
                                    →
                                    {{ $plan->ffinal->format('d/m/Y') }}
                                </p>
                            </div>
                            <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider rounded-md px-2 py-0.5
                                        bg-cyan-500/15 text-cyan-300 border border-cyan-500/20">
                                {{ $plan->tiempo_ejecucion }} {{ $plan->tiempo_ejecucion == 1 ? 'semana' : 'semanas' }}
                            </span>
                        </div>

                        <p class="mt-3 text-sm text-gray-300 line-clamp-3">
                            {{ $plan->justificacion ?: 'Sin diagnóstico.' }}
                        </p>

    

                        {{-- Progreso de estrategias: 0..50 --}}
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-[11px] text-gray-500 mb-1">
                                <span>Estrategias</span>
                                <span>{{ $escritas }} / {{ $total }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-white/5 overflow-hidden" role="progressbar"
                                aria-valuenow="{{ $escritas }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                                <div class="h-full bg-cyan-500 rounded-full"
                                    style="width: {{ $total > 0 ? round($escritas / $total * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones. Cada estrategia se abre en SU celda día/momento. --}}
                    <div class="p-3 border-t border-white/5 flex items-center gap-1.5">
                        <button wire:click="openModal('strategy', {{ $plan->id }})" type="button"
                            class="flex-1 text-xs px-2 py-1.5 rounded-lg bg-cyan-500/15 text-cyan-300 hover:bg-cyan-500/25 border border-cyan-500/20 transition-colors"
                            title="Editar estrategias día × momento">
                            Estrategias
                        </button>
                        <button wire:click="openModal('activity', {{ $plan->id }})" type="button"
                            class="flex-1 text-xs px-2 py-1.5 rounded-lg bg-purple-500/15 text-purple-300 hover:bg-purple-500/25 border border-purple-500/20 transition-colors"
                            title="Actividades por área de aprendizaje">
                            Actividades ({{ $actividades }})
                        </button>
                        <button wire:click="openModal('edit', {{ $plan->id }})" type="button"
                            class="p-2 rounded-lg bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/20 text-amber-300 transition-colors"
                            title="Editar plan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="openModal('view', {{ $plan->id }})" type="button"
                            class="p-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors"
                            title="Ver detalle">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <a href="{{ route('inicials.eispecialks.format', $plan->id) }}" target="_blank"
                            class="p-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors"
                            title="Formato imprimible">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z" />
                            </svg>
                        </a>
                        <button wire:click="confirmDeletePlan({{ $plan->id }})" type="button"
                            class="p-2 rounded-lg bg-white/5 hover:bg-red-500/20 border border-white/5 text-gray-400 hover:text-red-400 transition-colors"
                            title="Eliminar plan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
        @else
        {{-- ─── Vista tabla ─── --}}
        <div class="overflow-x-auto rounded-xl border border-white/10">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-xs uppercase tracking-wider text-gray-400">
                    <tr>
                        <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">Grado · Sección</th>
                        <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">Período</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium whitespace-nowrap">Estrategias</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium whitespace-nowrap">Actividades</th>
                        <th scope="col" class="px-3 py-2.5 text-left font-medium min-w-48">Justificación</th>
                        <th scope="col" class="px-3 py-2.5 text-right font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($eispecialks as $plan)
                        @php
                            $escritas = $plan->eispecialstrategies()->count();
                            $actividades = $plan->activities()->count();
                        @endphp
                        <tr class="align-top hover:bg-white/[0.02]">
                            <td class="px-3 py-2.5 text-gray-200 whitespace-nowrap font-medium">
                                {{ $plan->grado?->name ?? '—' }} · {{ $plan->seccion?->name ?? '—' }}
                            </td>
                            <td class="px-3 py-2.5 text-gray-300 whitespace-nowrap">
                                {{ $plan->finicial->format('d/m/Y') }} → {{ $plan->ffinal->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2.5 text-gray-300 text-right whitespace-nowrap tabular-nums">{{ $escritas }}/50</td>
                            <td class="px-3 py-2.5 text-gray-300 text-right whitespace-nowrap tabular-nums">{{ $actividades }}</td>
                            <td class="px-3 py-2.5 text-gray-400 min-w-48 max-w-xs truncate"
                                title="{{ $plan->justificacion }}">
                                {{ $plan->justificacion ?: '—' }}
                            </td>
                            <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openModal('edit', {{ $plan->id }})" type="button"
                                        class="p-1.5 rounded-md bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/20 text-amber-300 transition-colors"
                                        title="Editar plan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="openModal('strategy', {{ $plan->id }})" type="button"
                                        class="p-1.5 rounded-md bg-cyan-500/15 hover:bg-cyan-500/25 border border-cyan-500/20 text-cyan-300 transition-colors"
                                        title="Editar estrategias día × momento">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    </button>
                                    <button wire:click="openModal('activity', {{ $plan->id }})" type="button"
                                        class="p-1.5 rounded-md bg-purple-500/15 hover:bg-purple-500/25 border border-purple-500/20 text-purple-300 transition-colors"
                                        title="Actividades por área de aprendizaje">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </button>
                                    <button wire:click="openModal('view', {{ $plan->id }})" type="button"
                                        class="p-1.5 rounded-md bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors"
                                        title="Ver detalle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <a href="{{ route('inicials.eispecialks.format', $plan->id) }}" target="_blank"
                                        class="p-1.5 rounded-md bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors"
                                        title="Formato imprimible">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z" />
                                        </svg>
                                    </a>
                                    <button wire:click="confirmDeletePlan({{ $plan->id }})" type="button"
                                        class="p-1.5 rounded-md bg-white/5 hover:bg-red-500/20 border border-white/5 text-gray-400 hover:text-red-400 transition-colors"
                                        title="Eliminar plan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div>{{ $eispecialks->links() }}</div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         BLOQUE 3 · MODAL ÚNICO
         ═══════════════════════════════════════════════════════════ --}}
    @include('livewire.inicial.shared.import-wizard')

    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
            wire:key="modal-{{ $modalType }}-{{ $eispecialk_id }}">
            <div class="flex min-h-full items-end justify-center p-4 sm:items-start sm:pt-10 bg-gray-950/70 backdrop-blur-sm"
                wire:click.self="closeModal">

                <div class="w-full max-w-4xl rounded-xl bg-gray-900 border border-white/10 shadow-2xl"
                    x-data="{ open: true }" x-show="open"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0 translate-y-4">

                    {{-- Encabezado del modal --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
                        <h3 class="text-base font-semibold text-gray-100">
                            @switch($modalType)
                                @case('create')
                                    Nuevo plan especial
                                    @break
                                @case('edit')
                                    Editar plan especial
                                    @break
                                @case('view')
                                    Detalle del plan especial
                                    @break
                                @case('strategy')
                                    Estrategias · día × momento
                                    @break
                                @case('activity')
                                    Actividades por área de aprendizaje
                                    @break
                            @endswitch
                        </h3>
                        <button wire:click="closeModal" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-gray-200 hover:bg-white/5 transition-colors"
                            aria-label="Cerrar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Cuerpo --}}
                    <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
                        @includeWhen($modalType === 'create' || $modalType === 'edit', 'livewire.inicial.eispecialk.partials.plan-form')
                        @includeWhen($modalType === 'view', 'livewire.inicial.eispecialk.partials.plan-details')
                        @includeWhen($modalType === 'strategy', 'livewire.inicial.shared.strategy-wizard')
                        @includeWhen($modalType === 'activity' || $modalType === 'edit-activity', 'livewire.inicial.eispecialk.partials.activity-manager')
                    </div>

                    {{-- Pie --}}
                    <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-white/10 bg-gray-900/60 rounded-b-xl">
                        <button wire:click="closeModal" type="button"
                            class="rounded-lg px-4 py-2 text-sm text-gray-300 hover:bg-white/5 transition-colors">
                            {{ $modalType === 'view' ? 'Cerrar' : 'Cancelar' }}
                        </button>

                        @if ($modalType === 'create' || $modalType === 'edit')
                            <button wire:click="save" type="button"
                                class="rounded-lg bg-cyan-600 hover:bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition-colors">
                                Guardar plan
                            </button>
                        @endif

                        {{-- En AMBOS estados: el gestor muestra lista + formulario de alta,
                             así que sin esto la primera actividad no tenía dónde guardarse. --}}
                        @if ($modalType === 'activity' || $modalType === 'edit-activity')
                            <button wire:click="saveActivity" type="button"
                                class="rounded-lg bg-purple-600 hover:bg-purple-500 px-4 py-2 text-sm font-medium text-white transition-colors">
                                Guardar actividad
                            </button>
                        @endif

                        @if ($modalType === 'strategy')
                            <button wire:click="saveStrategies" type="button"
                                class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm font-medium text-gray-100 transition-colors">
                                Guardar todas
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
