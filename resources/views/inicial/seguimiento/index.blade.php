{{--
    Seguimiento de Educación Inicial (solo lectura).

    Controlador: App\Http\Controllers\Inicial\SeguimientoInicialController::listado
    Componente:  App\Livewire\Inicial\SeguimientoInicialComponent
    Ruta:        inicials.seguimiento.{entidad}.index
    Acceso:      auth + isInicialManager

    Blueprint: blueprint/inicial · doc 11 · §9.2.

    El shell solo aporta el marco: cabecera, insignia de solo lectura, botón
    de leyenda de estados y el componente. Las pestañas viven dentro del
    componente para que conserven los filtros cuando Livewire actualiza la URL.
--}}
<x-layouts.role>
    <div class="py-6">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <header class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <nav class="text-xs text-gray-500 mb-1" aria-label="Migas de pan">
                        <ol class="flex items-center gap-2">
                            <li class="text-gray-400">Educación Inicial</li>
                            <li aria-hidden="true">/</li>
                            <li class="text-gray-300" aria-current="page">Seguimiento</li>
                        </ol>
                    </nav>

                    <h1 class="text-lg font-semibold text-gray-100">
                        Seguimiento de Educación Inicial
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Consulta de todos los docentes. Los documentos no se crean ni se editan;
                        solo se registra la revisión.
                    </p>
                </div>

                <div class="flex items-center gap-2" x-data="{ leyenda: false }">
                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-cyan-500/20 bg-cyan-500/10 px-3 py-1.5 text-xs font-medium text-cyan-300"
                        title="Los documentos son de solo lectura; la revisión sí se registra.">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Solo lectura
                    </span>

                    <button type="button" x-on:click="leyenda = true"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-gray-300 transition-colors hover:bg-white/10 hover:text-white"
                        aria-haspopup="dialog" :aria-expanded="leyenda.toString()">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Leyenda de estados
                    </button>

                    @include('inicial.seguimiento.partials.leyenda-estados')
                </div>
            </header>

            <livewire:inicial.seguimiento-inicial-component
                :entidad="$entidad"
                :key="$entidad" />
        </div>
    </div>
</x-layouts.role>
