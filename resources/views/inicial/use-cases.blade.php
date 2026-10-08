{{--
    Casos de uso del módulo de Educación Inicial, como documentación viva.

    Controlador: App\Http\Controllers\Inicial\HomeInicialController::useCases
    Ruta:        inicials.use-cases → /app/inicials/use-cases

    ─────────────────────────────────────────────────────────────────────────────
    POR QUÉ SON DATOS Y NO MARCADO
    ─────────────────────────────────────────────────────────────────────────────
    El legacy tenía este contenido en la vista Y duplicado literalmente en los
    cuatro controladores de perspectiva. Aquí el arreglo vive en
    {@see \App\Http\Controllers\Inicial\HomeInicialController::casosDeUso()}, y
    cualquier otra perspectiva que quiera mostrarlos los reutiliza sin copiarlos.
--}}
<x-layouts.role>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <header>
                <nav class="text-xs text-gray-500 mb-1" aria-label="Migas de pan">
                    <ol class="flex items-center gap-2">
                        <li><a href="{{ route('inicials.home') }}" class="hover:text-cyan-400 transition-colors">Educación Inicial</a></li>
                        <li aria-hidden="true">/</li>
                        <li class="text-gray-300" aria-current="page">Casos de uso</li>
                    </ol>
                </nav>
                <h1 class="text-lg font-semibold text-gray-100">Casos de uso del módulo</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Los siete recorridos que cubre Educación Inicial, de la entrada del docente
                    al boletín impreso.
                </p>
            </header>

            <ol class="space-y-3">
                @foreach ($casos as $i => $caso)
                    <li class="flex gap-4 rounded-xl border border-white/10 bg-gray-900/60 p-4">
                        <span @class([
                                'shrink-0 w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold border',
                                'border-cyan-500/20 bg-cyan-500/10 text-cyan-300' => $caso['color'] === 'cyan',
                                'border-sky-500/20 bg-sky-500/10 text-sky-300' => $caso['color'] === 'sky',
                                'border-indigo-500/20 bg-indigo-500/10 text-indigo-300' => $caso['color'] === 'indigo',
                                'border-amber-500/20 bg-amber-500/10 text-amber-300' => $caso['color'] === 'amber',
                                'border-rose-500/20 bg-rose-500/10 text-rose-300' => $caso['color'] === 'rose',
                                'border-emerald-500/20 bg-emerald-500/10 text-emerald-300' => $caso['color'] === 'emerald',
                            ])>
                            {{ $i + 1 }}
                        </span>

                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-gray-100">{{ $caso['titulo'] }}</h2>
                            <p class="mt-1 text-xs text-gray-400 leading-relaxed">{{ $caso['descripcion'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>

            <p class="text-xs text-gray-500">
                <a href="{{ route('inicials.home') }}" class="text-cyan-400 hover:text-cyan-300">
                    Volver al módulo
                </a>
            </p>
        </div>
    </div>
</x-layouts.role>