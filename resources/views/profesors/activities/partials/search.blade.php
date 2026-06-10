<form method="GET" action="{{ route($route ?? 'app.profesors.activities.index') }}" class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        {{-- Plan de Estudio --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan de Estudio</label>
            <select name="pestudio_id" id="pestudio_id"
                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                <option value="">Todos</option>
                @foreach($list_pestudio as $id => $name)
                    <option value="{{ $id }}" {{ ($pestudio_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Grado --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grado</label>
            <select name="grado_id" id="grado_id"
                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                <option value="">Seleccione</option>
                @foreach($list_grado as $id => $name)
                    <option value="{{ $id }}" {{ ($grado_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Sección --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Sección</label>
            <select name="seccion_id" id="seccion_id"
                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                <option value="">Seleccione</option>
                @foreach($list_seccion as $id => $name)
                    <option value="{{ $id }}" {{ ($seccion_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Lapso --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Lapso</label>
            <select name="lapso_id" id="lapso_id"
                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                <option value="">Seleccione</option>
                @foreach($list_lapso as $id => $name)
                    <option value="{{ $id }}" {{ ($lapso_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Button --}}
        <div class="flex items-end">
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 rounded-xl border border-emerald-500/20 transition-all duration-200 text-xs font-bold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Buscar
            </button>
        </div>
    </div>
</form>

<script>
    document.getElementById('pestudio_id')?.addEventListener('change', function() {
        var pestudioId = this.value;
        var gradoSelect = document.getElementById('grado_id');
        var seccionSelect = document.getElementById('seccion_id');

        // Clear grado and seccion
        gradoSelect.innerHTML = '<option value="">Seleccione</option>';
        if (seccionSelect) {
            seccionSelect.innerHTML = '<option value="">Seleccione</option>';
        }

        if (pestudioId) {
            fetch('/app/profesors/activities/grados-by-pestudio/' + pestudioId)
                .then(function(response) { return response.json(); })
                .then(function(grados) {
                    grados.forEach(function(grado) {
                        var option = document.createElement('option');
                        option.value = grado.id;
                        option.textContent = grado.name;
                        gradoSelect.appendChild(option);
                    });
                })
                .catch(function() {
                    // fallback: form submit on next search will server-render correctly
                });
        }
    });

    document.getElementById('grado_id')?.addEventListener('change', function() {
        var gradoId = this.value;
        var seccionSelect = document.getElementById('seccion_id');

        seccionSelect.innerHTML = '<option value="">Seleccione</option>';

        if (gradoId) {
            fetch('/app/profesors/activities/secciones-by-grado/' + gradoId)
                .then(function(response) { return response.json(); })
                .then(function(secciones) {
                    secciones.forEach(function(seccion) {
                        var option = document.createElement('option');
                        option.value = seccion.id;
                        option.textContent = seccion.name;
                        seccionSelect.appendChild(option);
                    });
                })
                .catch(function() {
                    // fallback: form submit on next search will server-render correctly
                });
        }
    });
</script>
