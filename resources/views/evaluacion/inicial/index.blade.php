{{--
    Perspectiva de COORDINACIÓN DE EVALUACIÓN sobre Educación Inicial.

    Controlador: App\Http\Controllers\Evaluacion\InicialController::index
    Ruta:        evaluacions.inicials.index → /app/evaluacions/inicials
    Acceso:      auth + isDiagnostic

    ─────────────────────────────────────────────────────────────────────────────
    LAS 6 PESTAÑAS, TODAS HABILITADAS
    ─────────────────────────────────────────────────────────────────────────────
    El legacy las tenía todas activas (a diferencia de Académico, donde solo
    dos lo están) y cada una embebía un componente Livewire con los filtros como
    props. Aquí los componentes son 6 clases que comparten una base
    (`EvaluacionDocumentComponent`); el legacy tenía 7, una de ellas rota con un
    `dd()` en `render()`.

    El listado se mantiene en la pestaña activa para no ejecutar 6 consultas al
    cargar: cada componente solo consulta cuando se abre su pestaña.
--}}
<x-layouts.role>
    <div class="py-6">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ═══ Encabezado ═══ --}}
            <header class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <nav class="text-xs text-gray-500 mb-1" aria-label="Migas de pan">
                        <ol class="flex items-center gap-2">
                            <li><a href="{{ route('inicials.home') }}" class="hover:text-cyan-400 transition-colors">Educación Inicial</a></li>
                            <li aria-hidden="true">/</li>
                            <li class="text-gray-300" aria-current="page">Coordinación de Evaluación</li>
                        </ol>
                    </nav>
                    <h1 class="text-lg font-semibold text-gray-100">
                        Educación Inicial, formatos de planificación
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Revisión de los seis documentos del módulo. La Coordinación escribe
                        <span class="text-amber-300">observación</span> en los planes y
                        <span class="text-amber-300">recomendación</span> en las evaluaciones.
                    </p>
                </div>

                <a href="{{ route('inicials.home') }}"
                    class="text-xs text-gray-400 hover:text-cyan-300 transition-colors">
                    Volver al módulo
                </a>
            </header>

            {{-- ═══ FILTROS (GET) ═══ --}}
            <form method="GET" action="{{ route('evaluacions.inicials.index') }}"
                class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 items-end rounded-xl border border-white/10 bg-gray-900/60 p-4">
                <div>
                    <label for="filtro-profesor" class="block text-xs font-medium text-gray-400 mb-1">
                        Docente
                    </label>
                    <select id="filtro-profesor" name="profesor_id"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 outline-none">
                        <option value="">Todos</option>
                        @foreach ($listProfesores as $id => $nombre)
                            <option value="{{ $id }}" @selected((int) $profesor_id === (int) $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filtro-grado" class="block text-xs font-medium text-gray-400 mb-1">
                        Grado
                    </label>
                    <select id="filtro-grado" name="grado_id"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 outline-none">
                        <option value="">Todos</option>
                        @foreach ($listGrados as $id => $nombre)
                            <option value="{{ $id }}" @selected((int) $grado_id === (int) $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filtro-seccion" class="block text-xs font-medium text-gray-400 mb-1">
                        Sección
                    </label>
                    {{-- Las secciones se calculan en el servidor para el grado
                         recibido; cambiarlos sin recargar dejaría la lista
                         desfasada. Es el precio de no meter Livewire aquí, que es
                         lo que pedía el legacy. --}}
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
                    <a href="{{ route('evaluacions.inicials.index') }}"
                        class="rounded-lg px-3 py-2 text-sm text-gray-400 hover:text-gray-200 hover:bg-white/5 transition-colors">
                        Limpiar
                    </a>
                </div>
            </form>

            {{-- ═══ INDICADORES ═══ --}}
            @include('evaluacion.inicial.partials.stats')

            {{-- ═══ PESTAÑAS ═══ --}}
            @php
                $pestanas = [
                    'eiplanningwks' => 'Plan semanal',
                    'eiplanningbwks' => 'Plan quincenal',
                    'eiprojectks' => 'Proyecto de aula',
                    'eispecialks' => 'Plan especial',
                    'eievaluationks' => 'Plan de evaluación',
                    'eifinalks' => 'Informe final',
                ];
            @endphp

            <div x-data="{ activa: '{{ collect(array_keys($pestanas))->first() }}' }" class="space-y-4">
                <div class="flex flex-wrap items-center gap-1 border-b border-white/10" role="tablist">
                    @foreach ($pestanas as $clave => $etiqueta)
                        <button type="button" role="tab"
                            @click="activa = '{{ $clave }}'"
                            aria-selected="{{ 'activa === \''.$clave.'\'' ? 'true' : 'false' }}"
                            class="px-3 py-2 text-sm font-medium -mb-px border-b-2 transition-colors
                                   {{ 'activa === \''.$clave.'\'' ? 'border-cyan-500 text-cyan-300' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
                            {{ $etiqueta }}
                        </button>
                    @endforeach
                </div>

                @foreach ($pestanas as $clave => $etiqueta)
                    <div role="tabpanel" x-show="activa === '{{ $clave }}'" x-cloak>
                        {{-- Los componentes reciben los filtros como props: el
                             listado de cada pestaña usa exactamente los mismos
                             criterios que los contadores de arriba. --}}
                        @switch($clave)
                            @case('eiplanningwks')
                                <livewire:evaluacion.inicial.eiplanningwk-component
                                    :profesor-id="$profesor_id" :grado-id="$grado_id" :seccion-id="$seccion_id" />
                                @break
                            @case('eiplanningbwks')
                                <livewire:evaluacion.inicial.eiplanningbwk-component
                                    :profesor-id="$profesor_id" :grado-id="$grado_id" :seccion-id="$seccion_id" />
                                @break
                            @case('eiprojectks')
                                <livewire:evaluacion.inicial.eiprojectk-component
                                    :profesor-id="$profesor_id" :grado-id="$grado_id" :seccion-id="$seccion_id" />
                                @break
                            @case('eispecialks')
                                <livewire:evaluacion.inicial.eispecialk-component
                                    :profesor-id="$profesor_id" :grado-id="$grado_id" :seccion-id="$seccion_id" />
                                @break
                            @case('eievaluationks')
                                <livewire:evaluacion.inicial.eievaluationk-component
                                    :profesor-id="$profesor_id" :grado-id="$grado_id" :seccion-id="$seccion_id" />
                                @break
                            @case('eifinalks')
                                <livewire:evaluacion.inicial.eifinalk-component
                                    :profesor-id="$profesor_id" :grado-id="$grado_id" :seccion-id="$seccion_id" />
                                @break
                        @endswitch
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.role>