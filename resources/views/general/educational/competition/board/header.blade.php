<header class="bg-gray-900/80 backdrop-blur-sm border-b border-emerald-800/30 sticky top-0 z-10 shadow-lg">
    <div class="container-fluid mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Logo y título principal -->
            <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    <img src="{{ asset('image/brand/512.png') }}" alt="{{ config('app.name') }} Logo"
                        class="w-12 h-12 md:w-16 md:h-16 rounded-xl shadow-lg transition-transform duration-300 hover:scale-105 object-contain">
                </div>

                <div class="flex flex-col">
                    <h1 class="text-lg md:text-2xl font-bold text-white uppercase">CFLA - Pizarra</h1>
                    <p class="sm:block text-sm text-emerald-300 font-bold"><strong>Competiciones Estudiantiles.</strong>
                        Participación Interactiva</p>
                </div>
            </div>

            <div class="hidden w-full md:flex md:items-center md:w-auto space-x-4">
                <div
                    class="font-bold border border-emerald-500/30 rounded-lg px-4 py-2 bg-emerald-900/20 text-emerald-400 backdrop-blur-md">
                    Pizarra: {{ $token }}
                </div>
                <a href="{{ url('/') }}" target="_blank" class="btn-diagnostic">Dashboard</a>
            </div>
        </div>
    </div>
</header>
