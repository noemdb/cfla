{{--
    Formulario de la cabecera del plan semanal.
    Estados: create · edit. Solo lectura en `view` (ver plan-details).

    Reglas aplicadas: `App\Http\Requests\Inicial\EiplanningwkRequest`
    (R1–R3 del blueprint). El "diagnóstico ≥ 50 caracteres" que documentan los
    use-cases NO bloquea: el runtime legacy era min:10 y endurecerlo antes de
    migrar los datos expulsaría a los docentes. Se muestra como ayuda.
--}}
<div class="space-y-5">

    <div class="grid gap-4 sm:grid-cols-2">
        {{-- Grado → dispara la carga en cascada de secciones --}}
        <div>
            <label for="p-grado" class="block text-xs font-medium text-gray-400 mb-1.5">
                Grado <span class="text-red-400">*</span>
            </label>
            <select id="p-grado" wire:model.live="eiplanningwk.grado_id"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                <option value="">Seleccione un grado</option>
                @foreach ($listGrado as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eiplanningwk.grado_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="p-seccion" class="block text-xs font-medium text-gray-400 mb-1.5">
                Sección <span class="text-red-400">*</span>
            </label>
            <select id="p-seccion" wire:model.live="eiplanningwk.seccion_id"
                @disabled(! ($eiplanningwk['grado_id'] ?? null))
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none disabled:opacity-50">
                <option value="">Seleccione una sección</option>
                @foreach ($listSeccion as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eiplanningwk.seccion_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label for="p-inicial" class="block text-xs font-medium text-gray-400 mb-1.5">
                Fecha de inicio <span class="text-red-400">*</span>
            </label>
            {{-- Datepicker nativo: el legacy usaba `type="text"` y obligaba a
                 escribir la fecha a mano (bug doc 03). --}}
            <input id="p-inicial" type="date" wire:model.live="eiplanningwk.finicial"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eiplanningwk.finicial')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="p-ffinal" class="block text-xs font-medium text-gray-400 mb-1.5">
                Fecha de culminación <span class="text-red-400">*</span>
            </label>
            <input id="p-ffinal" type="date" wire:model.live="eiplanningwk.ffinal"
                min="{{ $eiplanningwk['finicial'] ?? null }}"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eiplanningwk.ffinal')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="p-semanas" class="block text-xs font-medium text-gray-400 mb-1.5">
                Cant. de semanas <span class="text-red-400">*</span>
            </label>
            {{-- R3: el legacy exigía 1 y lo dejaba manual. El cálculo
                 automático a partir de las fechas quedó en backlog. --}}
            <input id="p-semanas" type="number" min="1" step="1"
                wire:model.live="eiplanningwk.tiempo_ejecucion"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eiplanningwk.tiempo_ejecucion')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Proyecto vinculado --}}
    <div>
        <label for="p-proyecto" class="block text-xs font-medium text-gray-400 mb-1.5">
            Proyecto de aula vinculado <span class="text-gray-500">(opcional)</span>
        </label>
        <select id="p-proyecto" wire:model.live="eiplanningwk.eiprojectk_id"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            <option value="">Sin proyecto vinculado</option>
            @foreach ($listEiprojectk as $id => $descripcion)
                <option value="{{ $id }}">{{ $descripcion }}</option>
            @endforeach
        </select>
        @error('eiplanningwk.eiprojectk_id')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Diagnóstico --}}
    <div>
        <label for="p-diag" class="block text-xs font-medium text-gray-400 mb-1.5">
            Diagnóstico inicial <span class="text-red-400">*</span>
        </label>
        <textarea id="p-diag" rows="4" wire:model.live="eiplanningwk.diagnostico"
            placeholder="Situación de partida del grupo: intereses, observaciones, necesidades de aprendizaje…"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        {{-- R1: el mínimo que bloquea es 10 (runtime legacy). El "≥50" de los
             use-cases se muestra como recomendación, no como filtro. --}}
        <p class="mt-1 text-[11px] text-gray-600">
            Mínimo 10 caracteres. Se recomienda un diagnóstico de al menos 50 para que sea útil en la revisión del coordinador.
        </p>
        @error('eiplanningwk.diagnostico')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Observación --}}
    <div>
        <label for="p-obs" class="block text-xs font-medium text-gray-400 mb-1.5">
            Observación <span class="text-gray-500">(opcional)</span>
        </label>
        <textarea id="p-obs" rows="2" wire:model.live="eiplanningwk.observacion"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        @error('eiplanningwk.observacion')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
