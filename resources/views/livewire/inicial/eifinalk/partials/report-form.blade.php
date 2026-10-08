{{--
    Formulario del INFORME FINAL por estudiante.
    Estados: create · edit (un solo modal, `$modalType` distingue).

    ─────────────────────────────────────────────────────────────────────────────
    TRES COSAS QUE NO TIENEN EQUIVALENTE EN LOS OTROS DOCUMENTOS
    ─────────────────────────────────────────────────────────────────────────────
    1. La carga académica es OBLIGATORIA y de ella se derivan grado, sección,
       lapso y asignatura: el informe no repite esos datos.
    2. Los CAMPOS CONDICIONALES según `status_official`: un informe oficial pide
       logros, observaciones individuales y aprendizajes esperados; uno de
       componente pide la observación del especialista. El legacy lo tenía
       cableado con dos `@if` dentro del markup; aquí la condición sale de
       `$this->esOficial()` y las etiquetas de {@see camposOficiales()}.
    3. El ACORDEÓN DE EXPECTATIVAS, agrupado por área de aprendizaje del grado.

    Reglas: App\Http\Requests\Inicial\EifinalkRequest
--}}
<div class="space-y-5">

    @php $oficial = $this->esOficial(); @endphp

    {{-- ═══ Ancla: carga, estudiante, orden y título ═══ --}}
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="r-pevaluacion" class="block text-xs font-medium text-gray-400 mb-1.5">
                Carga académica <span class="text-red-400">*</span>
            </label>
            <select id="r-pevaluacion" wire:model.live="eifinalk.pevaluacion_id"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                <option value="">Seleccione una carga</option>
                @foreach ($listPevaluacion as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eifinalk.pevaluacion_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-[11px] text-gray-600">
                De esta carga se derivan el grado, la sección y el lapso del informe.
            </p>
        </div>

        <div>
            <label for="r-estudiant" class="block text-xs font-medium text-gray-400 mb-1.5">
                Estudiante <span class="text-red-400">*</span>
            </label>
            {{-- Se rellena desde la pestaña de estudiantes. Si el docente llega
                 por el botón "Nuevo informe" sin elegir carga, no hay sección de
                 la que sacar el listado: se acepta el id por búsqueda para no
                 dejar el formulario inutilizable. --}}
            @php $seleccionado = $this->nombreEstudiante($eifinalk['estudiant_id'] ?? null); @endphp
            <input id="r-estudiant" type="text" readonly
                value="{{ $seleccionado !== '—' ? $seleccionado : 'Sin estudiante seleccionado' }}"
                class="w-full bg-gray-900/60 border border-white/5 text-gray-400 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
            <input type="hidden" wire:model="eifinalk.estudiant_id">
            @error('eifinalk.estudiant_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="r-order" class="block text-xs font-medium text-gray-400 mb-1.5">
                Orden <span class="text-red-400">*</span>
            </label>
            <input id="r-order" type="number" min="1" step="1" wire:model.live="eifinalk.order"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eifinalk.order')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-[11px] text-gray-600">Goberna el orden de impresión del boletín.</p>
        </div>
    </div>

    <div>
        <label for="r-title" class="block text-xs font-medium text-gray-400 mb-1.5">
            Título del informe <span class="text-red-400">*</span>
        </label>
        <input id="r-title" type="text" wire:model.live="eifinalk.title"
            placeholder="Informe final · 1er MOMENTO · Lengua"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
        @error('eifinalk.title')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- ═══ Campos comunes ═══ --}}
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach ([
            'context_group' => ['Contexto del grupo', 'Situación de partida del grupo en esta área…', 3],
            'planing_eject' => ['Planeamiento', 'Cómo se planeó y se ejecutó el periodo.', 3],
            'featured_project' => ['Proyecto destacado', null, 2],
            'special_activities' => ['Actividades especiales', null, 2],
        ] as $campo => [$etiqueta, $placeholder, $filas])
            <div>
                <label for="r-{{ $campo }}" class="block text-xs font-medium text-gray-400 mb-1.5">
                    {{ $etiqueta }} <span class="text-gray-500">(opcional)</span>
                </label>
                <textarea id="r-{{ $campo }}" rows="{{ $filas }}" wire:model.live="eifinalk.{{ $campo }}"
                    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
                @error('eifinalk.{{ $campo }}')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </div>

    {{-- ═══ Campos CONDICIONALES según status_official ═══ --}}
    @if ($oficial)
        <div class="rounded-lg border border-emerald-500/20 bg-emerald-500/5 p-4 space-y-3">
            <p class="text-xs text-emerald-300 font-medium">
                Informe OFICIAL — esta carga está marcada como oficial.
            </p>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($this->camposOficiales() as $campo => $etiqueta)
                    <div @class(['sm:col-span-2' => $campo === 'individual_observations'])>
                        <label for="r-{{ $campo }}" class="block text-xs font-medium text-gray-400 mb-1.5">
                            {{ $etiqueta }} <span class="text-gray-500">(opcional)</span>
                        </label>
                        <textarea id="r-{{ $campo }}" rows="3" wire:model.live="eifinalk.{{ $campo }}"
                            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none"></textarea>
                        @error('eifinalk.{{ $campo }}')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-lg border border-amber-500/20 bg-amber-500/5 p-4 space-y-3">
            <p class="text-xs text-amber-300 font-medium">
                Informe de COMPONENTE — esta carga no está marcada como oficial.
            </p>
            @foreach ($this->camposComponente() as $campo => $etiqueta)
                <div>
                    <label for="r-{{ $campo }}" class="block text-xs font-medium text-gray-400 mb-1.5">
                        {{ $etiqueta }} <span class="text-gray-500">(opcional)</span>
                    </label>
                    <textarea id="r-{{ $campo }}" rows="3" wire:model.live="eifinalk.{{ $campo }}"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>
                    @error('eifinalk.{{ $campo }}')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ Cierre ═══ --}}
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach ([
            'family_participation' => ['Participación familiar', 'Cómo costar la familia en este periodo.', 3],
            'conclusions' => ['Conclusiones', null, 3],
            'recommendations' => ['Recomendaciones', null, 3],
        ] as $campo => [$etiqueta, $placeholder, $filas])
            <div>
                <label for="r-{{ $campo }}" class="block text-xs font-medium text-gray-400 mb-1.5">
                    {{ $etiqueta }} <span class="text-gray-500">(opcional)</span>
                </label>
                <textarea id="r-{{ $campo }}" rows="{{ $filas }}" wire:model.live="eifinalk.{{ $campo }}"
                    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
                @error('eifinalk.{{ $campo }}')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </div>

    {{-- ═══ ACORDEÓN DE EXPECTATIVAS ═══ --}}
    <div class="border-t border-white/10 pt-4">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">
                Expectativas de aprendizaje
            </h4>
            @if ($learningAreas->isNotEmpty())
                <span class="text-[11px] text-gray-500">
                    {{ count($selected_expectations) }} marcadas
                </span>
            @endif
        </div>

        @if (! $eifinalk['pevaluacion_id'])
            <p class="text-xs text-amber-400/90">
                Elige una carga académica para cargar las áreas y sus expectativas.
            </p>
        @elseif ($learningAreas->isEmpty())
            <p class="text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                No hay áreas de aprendizaje registradas para el grado de esta carga.
            </p>
        @else
            <div class="space-y-2" x-data="{ abierto: {} }">
                @foreach ($learningAreas as $area)
                    <div class="rounded-lg border border-white/10 bg-gray-800/40">
                        <button type="button"
                            @click="abierto['{{ $area->id }}'] = !abierto['{{ $area->id }}']"
                            class="w-full flex items-center justify-between gap-3 px-4 py-2.5 text-left">
                            <span class="text-sm text-gray-200">
                                {{ $area->name }}
                                <span class="text-[11px] text-gray-500">
                                    ({{ $area->expectations->count() }})
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform"
                                :class="abierto['{{ $area->id }}'] && 'rotate-180'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="abierto['{{ $area->id }}']" x-collapse class="px-4 pb-3 space-y-2">
                            @forelse ($area->expectations as $expectation)
                                <label class="flex items-start gap-2 text-sm text-gray-300 cursor-pointer">
                                    <input type="checkbox" wire:model="selected_expectations"
                                        value="{{ $expectation->id }}"
                                        @checked(in_array($expectation->id, array_map('intval', $selected_expectations)))
                                        class="mt-0.5 rounded border-white/20 bg-gray-900 text-cyan-600 focus:ring-cyan-500/50">
                                    <span>{{ $expectation->description }}</span>
                                </label>
                            @empty
                                <p class="text-xs text-gray-500 italic">Esta área no tiene expectativas registradas.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>