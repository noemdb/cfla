{{--
    Perspectiva de PLANIFICACIÓN sobre Educación Inicial.

    Controlador: App\Http\Controllers\Planning\InicialController::index
    Ruta:        plannings.inicials.index → /app/plannings/inicials
    Acceso:      auth + isPlanner

    ─────────────────────────────────────────────────────────────────────────────
    SOLO LECTURA
    ─────────────────────────────────────────────────────────────────────────────
    No hay ningún botón de escritura en esta pantalla: Planificación consulta y
    compara, no interviene. Las pestañas se cambian por GET (`?pestana=`), de modo
    que cada enlace es compartible y el navegador puede indexarlo.
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
                            <li class="text-gray-300" aria-current="page">Planificación</li>
                        </ol>
                    </nav>
                    <h1 class="text-lg font-semibold text-gray-100">
                        Educación Inicial, formatos de planificación
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Consulta de los cinco documentos de planificación. Sin opciones de escritura.
                    </p>
                </div>

                <a href="{{ route('inicials.home') }}" class="text-xs text-gray-400 hover:text-cyan-300 transition-colors">
                    Volver al módulo
                </a>
            </header>

            {{-- ═══ FILTROS (GET) ═══ --}}
            @include('inicial.shared.filters', ['rutaFiltros' => route('plannings.inicials.index')])

            {{-- ═══ Pestañas ═══ --}}
            @php
                $rutaBase = route('plannings.inicials.index', array_filter([
                    'profesor_id' => $profesor_id,
                    'grado_id' => $grado_id,
                    'seccion_id' => $seccion_id,
                ]));
            @endphp

            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-1 border-b border-white/10" role="tablist">
                    @foreach ($pestanas as $clave)
                        <a href="{{ $rutaBase }}&pestana={{ $clave }}" role="tab"
                            aria-selected="{{ $pestana === $clave ? 'true' : 'false' }}"
                            aria-current="{{ $pestana === $clave ? 'page' : 'false' }}"
                            @class([
                                'px-3 py-2 text-sm font-medium -mb-px border-b-2 transition-colors',
                                'border-cyan-500 text-cyan-300' => $pestana === $clave,
                                'border-transparent text-gray-500 hover:text-gray-300' => $pestana !== $clave,
                            ])>
                            {{ \App\Services\Inicial\RegistroInicial::titulo($clave) }}
                        </a>
                    @endforeach

                    {{-- El legacy tenía aquí una pestaña «Informe Pedagógico» con
                         el texto «Sin formato.» y ningún botón. Se conserva la
                         entrada, pero diciendo la verdad: los informes finales sí
                         existen y se revisan en la otra perspectiva. --}}
                    <span role="tab" aria-selected="false"
                        class="px-3 py-2 text-sm font-medium -mb-px border-b-2 border-transparent text-gray-600 cursor-not-allowed"
                        title="Los informes finales se revisan en la perspectiva de Coordinación de Evaluación">
                        Informe final
                    </span>
                </div>

                {{-- Una sola tabla por carga: la de la pestaña activa. --}}
                @include('inicial.shared.document-table', [
                    'entidad' => $pestana,
                    'documentos' => $documentos,
                    'rutaPrefijo' => 'plannings.inicials',
                    'mostrarRevision' => true,
                ])
            </div>
        </div>
    </div>
</x-layouts.role>