{{--
    Formulario de la cabecera del proyecto de aula.
    Estados: create · edit. Solo lectura en `view` (ver plan-details).

    Reglas aplicadas: `App\Http\Requests\Inicial\EiprojectkRequest`
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
            <select id="p-grado" wire:model.live="eiprojectk.grado_id"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                <option value="">Seleccione un grado</option>
                @foreach ($listGrado as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eiprojectk.grado_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="p-seccion" class="block text-xs font-medium text-gray-400 mb-1.5">
                Sección <span class="text-red-400">*</span>
            </label>
            <select id="p-seccion" wire:model.live="eiprojectk.seccion_id"
                @disabled(! ($eiprojectk['grado_id'] ?? null))
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none disabled:opacity-50">
                <option value="">Seleccione una sección</option>
                @foreach ($listSeccion as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eiprojectk.seccion_id')
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
            <input id="p-inicial" type="date" wire:model.live="eiprojectk.finicial"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eiprojectk.finicial')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="p-ffinal" class="block text-xs font-medium text-gray-400 mb-1.5">
                Fecha de culminación <span class="text-red-400">*</span>
            </label>
            <input id="p-ffinal" type="date" wire:model.live="eiprojectk.ffinal"
                min="{{ $eiprojectk['finicial'] ?? null }}"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eiprojectk.ffinal')
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
                wire:model.live="eiprojectk.tiempo_ejecucion"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eiprojectk.tiempo_ejecucion')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Nota: a diferencia de la semanal y la quincenal, `eiprojectks` NO tiene
         columna `eiprojectk_id`. La relación va al revés —son los planes los que
         apuntan al proyecto— y la gestiona el componente de planificación. --}}

    {{-- Diagnóstico --}}
    <div>
        <label for="p-diag" class="block text-xs font-medium text-gray-400 mb-1.5">
            Diagnóstico inicial <span class="text-red-400">*</span>
        </label>
        <textarea id="p-diag" rows="4" wire:model.live="eiprojectk.diagnostico"
            placeholder="Situación de partida del grupo: intereses, observaciones, necesidades de aprendizaje…"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        {{-- R1: el mínimo que bloquea es 10 (runtime legacy). El "≥50" de los
             use-cases se muestra como recomendación, no como filtro. --}}
        <p class="mt-1 text-[11px] text-gray-600">
            Mínimo 10 caracteres. Se recomienda un diagnóstico de al menos 50 para que sea útil en la revisión del coordinador.
        </p>
        @error('eiprojectk.diagnostico')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Observación --}}
    <div>
        <label for="p-obs" class="block text-xs font-medium text-gray-400 mb-1.5">
            Observación <span class="text-gray-500">(opcional)</span>
        </label>
        <textarea id="p-obs" rows="2" wire:model.live="eiprojectk.observacion"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        @error('eiprojectk.observacion')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
