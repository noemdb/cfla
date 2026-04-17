<header class="bg-white/90 backdrop-blur-sm border-b border-emerald-200 sticky top-0 z-10 shadow-md">
    <div class="container-fluid mx-auto px-4 py-3">
        <div class="flex items-center justify-between">

            {{-- Logo y título principal --}}
            <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    <img src="{{ asset('image/logo/logo1x1.png') }}"
                        alt="{{ config('app.name') }} Logo"
                        class="w-12 h-12 md:w-16 md:h-16 rounded-xl shadow-md transition-transform duration-300 hover:scale-105 object-contain">
                </div>
                <div class="flex flex-col">
                    <h1 class="text-lg md:text-2xl font-bold text-gray-900 uppercase">
                        CFLA - Pizarra
                    </h1>
                    <p class="sm:block text-sm text-emerald-700 font-bold">
                        <strong>Competiciones Estudiantiles.</strong> Preguntas y respuestas
                    </p>
                </div>
            </div>

            {{-- Derecha: badge + logo --}}
            <div class="flex items-center space-x-4">
                <div class="hidden w-full md:block md:w-auto">
                    <div class="font-bold border border-emerald-500 rounded-lg px-4 py-2 bg-emerald-50 text-emerald-700">
                        Pizarra de Resultados
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <img src="{{ asset('image/brand/512.png') }}"
                        alt="{{ config('app.name') }} Logo"
                        class="w-12 h-12 md:w-16 md:h-16 rounded-xl shadow-md transition-transform duration-300 hover:scale-105 object-contain">
                </div>
            </div>

        </div>
    </div>
</header>