{{--
    Formulario de la cabecera del plan de evaluación.
    Estados: create · edit. Solo lectura en `view` (ver plan-details).

    ─────────────────────────────────────────────────────────────────────────────
    LA CABECERA MÁS DISTINTA DEL MÓDULO
    ─────────────────────────────────────────────────────────────────────────────
    · El LAPSO es obligatorio: es el único documento anclado a un periodo. Al
      cambiarlo se recargan las áreas evaluables (`updatedEievaluationkLapsoId`).
    · NO hay `tiempo_ejecucion` ni `diagnostico`. El plan se documenta con
      OBSERVACIONES (en plural), ASISTENCIA y RECOMENDACIÓN.
    · `recomendacion` es el campo que escribe la Coordinación desde la
      perspectiva de evaluación (F5), así que se rotula como tal.

    Reglas aplicadas: App\Http\Requests\Inicial\EievaluationkRequest
    (R1–R2 del blueprint). El "≥50 caracteres" que documentan los use-cases NO
    bloquea: el runtime legacy era min:10 y se muestra como recomendación.
--}}
<div class="space-y-5">

    <div class="grid gap-4 sm:grid-cols-2">
        {{-- Grado → dispara la carga en cascada de secciones --}}
        <div>
            <label for="e-grado" class="block text-xs font-medium text-gray-400 mb-1.5">
                Grado <span class="text-red-400">*</span>
            </label>
            <select id="e-grado" wire:model.live="eievaluationk.grado_id"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                <option value="">Seleccione un grado</option>
                @foreach ($listGrado as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eievaluationk.grado_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="e-seccion" class="block text-xs font-medium text-gray-400 mb-1.5">
                Sección <span class="text-red-400">*</span>
            </label>
            <select id="e-seccion" wire:model.live="eievaluationk.seccion_id"
                @disabled(! ($eievaluationk['grado_id'] ?? null))
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none disabled:opacity-50">
                <option value="">Seleccione una sección</option>
                @foreach ($listSeccion as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eievaluationk.seccion_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        {{-- El lapso es obligatorio y propio de este documento --}}
        <div>
            <label for="e-lapso" class="block text-xs font-medium text-gray-400 mb-1.5">
                Lapso <span class="text-red-400">*</span>
            </label>
            <select id="e-lapso" wire:model.live="eievaluationk.lapso_id"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                <option value="">Seleccione un lapso</option>
                @foreach ($listLapso as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @error('eievaluationk.lapso_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="e-inicial" class="block text-xs font-medium text-gray-400 mb-1.5">
                Fecha de inicio <span class="text-red-400">*</span>
            </label>
            {{-- Datepicker nativo: el legacy usaba `type="text"` y obligaba a
                 escribir la fecha a mano (bug doc 03). --}}
            <input id="e-inicial" type="date" wire:model.live="eievaluationk.finicial"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eievaluationk.finicial')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="e-ffinal" class="block text-xs font-medium text-gray-400 mb-1.5">
                Fecha de culminación <span class="text-red-400">*</span>
            </label>
            <input id="e-ffinal" type="date" wire:model.live="eievaluationk.ffinal"
                min="{{ $eievaluationk['finicial'] ?? null }}"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            @error('eievaluationk.ffinal')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Observaciones (equivalente al diagnóstico de los otros documentos) --}}
    <div>
        <label for="e-obs" class="block text-xs font-medium text-gray-400 mb-1.5">
            Observaciones <span class="text-red-400">*</span>
        </label>
        <textarea id="e-obs" rows="4" wire:model.live="eievaluationk.observaciones"
            placeholder="Qué se evaluó en el periodo, cómo respondió el grupo, qué quedó pendiente…"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        {{-- R1: el mínimo que bloquea es 10 (runtime legacy). --}}
        <p class="mt-1 text-[11px] text-gray-600">
            Mínimo 10 caracteres. Se recomienda al menos 50 para que sea útil en la revisión del coordinador.
        </p>
        @error('eievaluationk.observaciones')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Asistencia: obligatorio en el runtime legacy --}}
    <div>
        <label for="e-asist" class="block text-xs font-medium text-gray-400 mb-1.5">
            Asistencia <span class="text-red-400">*</span>
        </label>
        <textarea id="e-asist" rows="2" wire:model.live="eievaluationk.asistencia"
            placeholder="Resumen de asistencia del grupo durante el periodo"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        @error('eievaluationk.asistencia')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Recomendación: la escribe el docente aquí y la Coordinación en F5 --}}
    <div>
        <label for="e-reco" class="block text-xs font-medium text-gray-400 mb-1.5">
            Recomendación <span class="text-gray-500">(opcional)</span>
        </label>
        <textarea id="e-reco" rows="2" wire:model.live="eievaluationk.recomendacion"
            placeholder="Qué se recomienda para el periodo siguiente"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        @error('eievaluationk.recomendacion')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Observación suelta: columna del DDL que el legacy no mencionaba --}}
    <div>
        <label for="e-obs2" class="block text-xs font-medium text-gray-400 mb-1.5">
            Observación adicional <span class="text-gray-500">(opcional)</span>
        </label>
        <textarea id="e-obs2" rows="2" wire:model.live="eievaluationk.observacion"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none"></textarea>
        @error('eievaluationk.observacion')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>