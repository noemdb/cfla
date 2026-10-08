{{--
    Filtros (GET) compartidos por las perspectivas de solo lectura.

    Controladores: App\Http\Controllers\Planning\InicialController@index
                   App\Http\Controllers\Academico\InicialController@index

    ─────────────────────────────────────────────────────────────────────────────
    POR QUÉ GET Y NO LIVEWIRE
    ─────────────────────────────────────────────────────────────────────────────
    Son filtros de consulta, no de edición: no hay estado que sincronizar, así que
    un componente Livewire solo añadiría un round-trip al servidor para leer tres
    desplegables. Con GET el filtro sobrevive a un F5 y la URL se puede compartir o
    mandar por correo.

    El precio conocido: las secciones se calculan en el servidor para el grado
    recibido, así que cambiar de grado obliga a pulsar «Buscar». Es lo que hacía el
    legacy y evita que el desplegable de secciones muestre secciones de otro
    grado.
--}}
@php
    // Un mismo parcial para las dos perspectivas: solo cambia el action y, en
    // Académico, que la sección se ofrece igual (su alcance son solo 2
    // documentos, pero el filtro es el mismo).
    $accion = $rutaFiltros ?? route('plannings.inicials.index');
@endphp

<form method="GET" action="{{ $accion }}"
    class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 items-end rounded-xl border border-white/10 bg-gray-900/60 p-4">
    <div>
        <label for="filtro-profesor" class="block text-xs font-medium text-gray-400 mb-1">Docente</label>
        <select id="filtro-profesor" name="profesor_id"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 outline-none">
            <option value="">Todos</option>
            @foreach ($listProfesores as $id => $nombre)
                <option value="{{ $id }}" @selected((int) $profesor_id === (int) $id)>{{ $nombre }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="filtro-grado" class="block text-xs font-medium text-gray-400 mb-1">Grado</label>
        <select id="filtro-grado" name="grado_id"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 outline-none">
            <option value="">Todos</option>
            @foreach ($listGrados as $id => $nombre)
                <option value="{{ $id }}" @selected((int) $grado_id === (int) $id)>{{ $nombre }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="filtro-seccion" class="block text-xs font-medium text-gray-400 mb-1">Sección</label>
        <select id="filtro-seccion" name="seccion_id"
            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 outline-none"
            @disabled($listSecciones->isEmpty())>
            <option value="">{{ $listSecciones->isEmpty() ? 'Elija primero un grado' : 'Todas' }}</option>
            @foreach ($listSecciones as $id => $nombre)
                <option value="{{ $id }}" @selected((int) $seccion_id === (int) $id)>{{ $nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex items-center gap-2">
        <button type="submit"
            class="rounded-lg bg-cyan-600 hover:bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition-colors">
            Buscar
        </button>
        <a href="{{ $accion }}"
            class="rounded-lg px-3 py-2 text-sm text-gray-400 hover:text-gray-200 hover:bg-white/5 transition-colors">
            Limpiar
        </a>
    </div>
</form>