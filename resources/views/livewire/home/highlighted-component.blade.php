<div class="space-y-6">

    <div class="text-center mb-8">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Acceso Rápido</h2>
        <div class="h-1 w-20 bg-emerald-500 mx-auto rounded-full"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Census Column -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl p-4 h-full flex flex-col hover:border-emerald-500/50 transition-all duration-300">
            @include('livewire.home.highlighted.census')

            @if ($showVideo)
                <!-- Pantalla de Video -->
                <div class="fixed inset-0 p-4 flex items-center justify-center bg-black/95 z-50 backdrop-blur-md">
                    <div class="relative w-full max-w-4xl mx-auto">
                        <video id="introVideo" class="w-full rounded-xl shadow-2xl border border-emerald-500/30" autoplay
                            muted controls>
                            <source src="{{ asset('videos/census/newCatch.mp4') }}" type="video/mp4">
                            Tu navegador no soporta videos.
                        </video>

                        <!-- Botón para Saltar Intro -->
                        <button wire:click="hideVideo"
                            class="absolute -top-12 right-0 text-white/80 hover:text-white hover:bg-white/10 px-4 py-2 rounded-lg transition flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Cerrar</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Payment Column -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl p-4 h-full flex flex-col hover:border-emerald-500/50 transition-all duration-300">
            <livewire:app.payment.index-component />
        </div>

        <!-- Info Payment Column -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl p-4 h-full flex flex-col hover:border-emerald-500/50 transition-all duration-300">
            @include('home.highlighted.infoPayment')
        </div>

        <!-- Point Column -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl p-4 h-full flex flex-col hover:border-emerald-500/50 transition-all duration-300">
            @include('home.highlighted.suspended.point')
        </div>

    </div>

</div>

@section('scriptsLivewire')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let video = document.getElementById('introVideo');
            video.onended = function() {
                Livewire.dispatch('hideVideo'); // Llama a la función Livewire al terminar
                console.log('close.video');
            };
        });
    </script>
@endsection
