<div class="h-full flex flex-col relative">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-2">
        <div class="p-2 bg-emerald-900/50 rounded-lg border border-emerald-500/30">
            <x-icon name="bars-3" class="w-8 h-8 text-emerald-400" />
        </div>
        <h3 class="text-lg md:text-lg font-bold text-emerald-100 uppercase tracking-wide">
            Censo Escolar 26-27 - Asistente
        </h3>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col">
        <div class="relative overflow-hidden rounded-lg mb-2 group">
            <div class="flex justify-center bg-gray-800/50 p-6 rounded-lg border border-emerald-500/20">
                <div
                    class="grid place-items-center h-24 w-24 bg-emerald-900/30 rounded-full border border-emerald-500/30 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                    <x-icon name="document-chart-bar" class="w-12 h-12 text-emerald-400" />
                </div>
            </div>

            <div class="text-center mt-4">
                <div class="text-lg font-semibold text-gray-200 mb-2">El primer paso hacia una educación de excelencia.
                </div>
                @php $jornadaProxima = App\Models\app\Academy\Catchment::getJornadaProxima(); @endphp
                <div class="text-sm text-emerald-300 font-medium mb-2">{{ $jornadaProxima['label'] }}, de 8am a 12m.</div>
            </div>
        </div>

        <p class="text-sm text-gray-400 text-center mb-6 leading-relaxed flex-1">
            Nos complace poder ofrecerles a sus hijos la oportunidad de formar parte de nuestra comunidad educativa, que
            está comprometida con la excelencia académica y el desarrollo integral de los estudiantes.
        </p>

        <div class="mt-auto">
            <x-button positive
                class="w-full bg-emerald-600 hover:bg-emerald-500 border-none shadow-lg shadow-emerald-500/20"
                :href="route('census')">
                Comenzar
            </x-button>
        </div>
    </div>

    @if ($showVideo)
        <!-- Pantalla de Video (overlay solo dentro del card) -->
        <div class="absolute inset-0 z-10 flex items-center justify-center bg-gray-900/95 backdrop-blur-sm rounded-lg p-4">
            <div class="relative w-full">
                <video id="introVideo" class="w-full h-[480px] rounded-lg border border-emerald-500/30 shadow-xl"
                    autoplay muted controls playsinline>
                    <source src="{{ asset('videos/census/newCatch.mp4') }}" type="video/mp4">
                    Tu navegador no soporta videos.
                </video>

                <!-- Botón para Cerrar -->
                <button wire:click="hideVideo"
                    class="absolute top-2 right-2 z-10 inline-flex items-center space-x-1.5 bg-black/50 hover:bg-black/70 text-white/90 hover:text-white px-3 py-1.5 rounded-lg border border-white/20 backdrop-blur-sm text-xs font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Cerrar</span>
                </button>
            </div>
        </div>
    @endif
</div>
