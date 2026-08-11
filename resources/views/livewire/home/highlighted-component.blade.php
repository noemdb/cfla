<div class="space-y-6">

    <div class="text-center mb-8">
        <h2 class="text-lg md:text-lg font-bold text-white mb-2">Acceso Rápido</h2>
        <div class="h-1 w-20 bg-emerald-500 mx-auto rounded-full"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Census Column -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-lg p-4 h-full flex flex-col relative overflow-hidden min-h-[520px] hover:border-emerald-500/50 transition-all duration-300">
            @include('livewire.home.highlighted.census')
        </div>

        <!-- Info Payment Column -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-lg p-4 h-full flex flex-col hover:border-emerald-500/50 transition-all duration-300">
            @include('home.highlighted.infoPayment')
        </div>

        <!-- Point Column -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-lg p-4 h-full flex flex-col hover:border-emerald-500/50 transition-all duration-300">
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
