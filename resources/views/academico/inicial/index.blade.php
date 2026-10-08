{{--
    Perspectiva ACADÉMICA / DIRECCIÓN sobre Educación Inicial.

    Controlador: App\Http\Controllers\Academico\InicialController::index
    Ruta:        academicos.inicials.index → /app/academicos/inicials
    Acceso:      auth + isAdmin

    ─────────────────────────────────────────────────────────────────────────────
    SOLO DOS DOCUMENTOS, Y POR QUÉ
    ─────────────────────────────────────────────────────────────────────────────
    Dirección necesita el plan de clase: semanal y proyecto de aula. Las otras
    cuatro pestañas se muestran DESHABILITADAS, cada una con el motivo y dónde se
    revisan.

    El legacy hacía lo mismo pero con un placeholder «Content N» (4 veces) y con
    un bug de sintaxis en el href del proyecto de aula. Aquí el motivo de cada
    bloqueo viene del controlador ({@see \App\Http\Controllers\Academico\InicialController::index}),
    que es donde se aplica la decisión de alcance, y los enlaces se construyen a
    partir de la entidad, así que no hay href escrito a mano.
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
                            <li class="text-gray-300" aria-current="page">Académico</li>
                        </ol>
                    </nav>
                    <h1 class="text-lg font-semibold text-gray-100">
                        Educación Inicial, formatos de planificación
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Solo lectura. Dirección consulta el plan semanal y el proyecto de aula.
                    </p>
                </div>

                <a href="{{ route('inicials.home') }}" class="text-xs text-gray-400 hover:text-cyan-300 transition-colors">
                    Volver al módulo
                </a>
            </header>

            {{-- ═══ FILTROS (GET) ═══ --}}
            @include('inicial.shared.filters', ['rutaFiltros' => route('academicos.inicials.index')])

            {{-- ═══ Pestañas ═══ --}}
            @php
                $rutaBase = route('academicos.inicials.index', array_filter([
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

                    @foreach ($noDisponibles as $clave => $motivo)
                        <span role="tab" aria-selected="false" title="{{ $motivo }}"
                            class="px-3 py-2 text-sm font-medium -mb-px border-b-2 border-transparent text-gray-600 cursor-not-allowed">
                            {{ \App\Services\Inicial\RegistroInicial::titulo($clave) }}
                        </span>
                    @endforeach
                </div>

                {{-- Tabla de la pestaña activa (solo lectura; sin la columna de
                     revisión, que es de quien escribe). --}}
                @include('inicial.shared.document-table', [
                    'entidad' => $pestana,
                    'documentos' => $documentos,
                    'rutaPrefijo' => 'academicos.inicials',
                    'mostrarRevision' => false,
                ])

                <details class="rounded-lg border border-white/10 bg-gray-900/40 px-4 py-3">
                    <summary class="cursor-pointer text-xs font-medium text-gray-400 hover:text-gray-200 transition-colors">
                        ¿Por qué las otras cuatro pestañas no están disponibles?
                    </summary>
                    <ul class="mt-3 space-y-1.5 text-xs text-gray-500">
                        @foreach ($noDisponibles as $clave => $motivo)
                            <li>
                                <span class="text-gray-300">{{ \App\Services\Inicial\RegistroInicial::titulo($clave) }}</span>
                                — {{ $motivo }}
                            </li>
                        @endforeach
                    </ul>
                </details>
            </div>
        </div>
    </div>
</x-layouts.role>