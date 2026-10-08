{{--
    Índice de Planificación Semanal (Educación Inicial).

    Controlador: App\Http\Controllers\Inicial\Tab\EiplanningwkController::index
    Componente: App\Livewire\Inicial\EiplanningwkComponent
    Ruta:       inicials.eiplanningwks.index  →  /app/inicials/eiplanningwks
    Acceso:     auth + isInicial
--}}
<x-layouts.role>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Migas de pan del módulo --}}
            <nav class="mb-4 text-xs text-gray-500" aria-label="Migas de pan">
                <ol class="flex items-center gap-2">
                    <li>
                        <a href="{{ route('inicials.home') }}" class="hover:text-cyan-400 transition-colors">
                            Educación Inicial
                        </a>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="text-gray-300" aria-current="page">Planificación Semanal</li>
                </ol>
            </nav>

            {{--
                Componente único full-page: listado, filtros, modal único y
                wizard de estrategias. Es el patrón del proyecto para páginas
                interactivas (CLAUDE.md · "Livewire over Controllers").
            --}}
            <livewire:inicial.eiplanningwk-component />
        </div>
    </div>
</x-layouts.role>
