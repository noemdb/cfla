{{--
    Formulario de la cabecera del plan especial.
    Estados: create · edit. Solo lectura en `view` (ver plan-details).

    DIFERENCIA con el formulario de la semanal y la quincenal: aquí el campo de
    fondo es `justificacion`, no `diagnostico`. El plan especial se argumenta por
    una razón pedagógica concreta, no por una descripción del grupo.

    Reglas aplicadas: `App\Http\Requests\Inicial\EispecialkRequest`
    (R1–R3 del blueprint). El "≥50 caracteres" que documentan los use-cases NO
    bloquea: el runtime legacy era min:10 y endurecerlo antes de migrar los
    datos expulsaría a los docentes. Se muestra como ayuda.
--}}
<div class="space-y-5">

    <div class="grid gap-4 sm:grid-cols-2">
        {{-- Grado → dispara la carga en cascada de secciones --}}
        <div>
            <label for="p-grado" class="block text-xs font-medium text-gray-400 mb-1.5">
                Grado <span class="text-red-400">*</span>
            </label>
            <select id="p-grado" wire:model.live="eispecialk.grado_id"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                <option value="">Seleccione un grado</option>
                @foreach ($listGrado as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eispecialk.grado_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="p-seccion" class="block text-xs font-medium text-gray-400 mb-1.5">
                Sección <span class="text-red-400">*</span>
            </label>
            <select id="p-seccion" wire:model.live="eispecialk.seccion_id"
                @disabled(! ($eispecialk['grado_id'] ?? null))
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none disabled:opacity-50">
                <option value="">Seleccione una sección</option>
                @foreach ($listSeccion as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eispecialk.seccion_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Área de aprendizaje (opcional): solo las del docente en su lapso en curso. --}}
    <div>
        <label for="p-pensum" class="block text-xs font-medium text-gray-400 mb-1.5">
            Área de aprendizaje <span class="text-gray-500">(opcional)</span>
        </label>
        <select id="p-pensum" wire:model.live="eispecialk.pensum_id"
            @disabled(! ($eispecialk['grado_id'] ?? null))
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none disabled:opacity-50">
            <option value="">Sin área vinculada</option>
            @foreach ($listPensum as $id => $descripcion)
                <option value="{{ $id }}">{{ $descripcion }}</option>
            @endforeach
        </select>
        @error('eispecialk.pensum_id')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-[11px] text-gray-600">Solo las áreas de tu carga académica en este lapso.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label for="p-inicial" class="block text-xs font-medium text-gray-400 mb-1.5">
                Fecha de inicio <span class="text-red-400">*</span>
            </label>
            {{-- Datepicker nativo: el legacy usaba `type="text"` y obligaba a
                 escribir la fecha a mano (bug doc 03). --}}
            <input id="p-inicial" type="date" wire:model.live="eispecialk.finicial"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eispecialk.finicial')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="p-ffinal" class="block text-xs font-medium text-gray-400 mb-1.5">
                Fecha de culminación <span class="text-red-400">*</span>
            </label>
            <input id="p-ffinal" type="date" wire:model.live="eispecialk.ffinal"
                min="{{ $eispecialk['finicial'] ?? null }}"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eispecialk.ffinal')
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
                wire:model.live="eispecialk.tiempo_ejecucion"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eispecialk.tiempo_ejecucion')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Nota: `eispecialks` NO tiene columna `eiprojectk_id` (la semanal y la
         quincenal sí). El plan especial no se subordina a ningún plan. --}}

    {{-- Justificación --}}
    <div>
        <label for="p-just" class="block text-xs font-medium text-gray-400 mb-1.5">
            Justificación del plan <span class="text-red-400">*</span>
        </label>
        <textarea id="p-just" rows="4" wire:model.live="eispecialk.justificacion"
            placeholder="Por qué se hace este plan especial y qué respuesta pedagógica da a una necesidad concreta del grupo…"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        {{-- R1: el mínimo que bloquea es 10 (runtime legacy). El "≥50" de los
             use-cases se muestra como recomendación, no como filtro. --}}
        <p class="mt-1 text-[11px] text-gray-600">
            Mínimo 10 caracteres. Se recomienda una justificación de al menos 50 para que sea útil en la revisión del coordinador.
        </p>
        @error('eispecialk.justificacion')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Observación --}}
    <div>
        <label for="p-obs" class="block text-xs font-medium text-gray-400 mb-1.5">
            Observación <span class="text-gray-500">(opcional)</span>
        </label>
        <textarea id="p-obs" rows="2" wire:model.live="eispecialk.observacion"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        @error('eispecialk.observacion')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
