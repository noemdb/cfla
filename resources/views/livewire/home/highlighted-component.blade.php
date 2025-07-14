<div>

    <x-card title="Mini App">

        <div class="grid grid-cols-12 gap-">

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl h-full">
                {{-- <livewire:app.enrollment.main-component /> --}}
                {{-- <livewire:catchment-wizard  />  --}}

                @include('livewire.home.highlighted.census')


                @if ($showVideo)
                    <!-- Pantalla de Video -->
                    <div class="fixed inset-0 p-4 rounded shadow flex items-center justify-center bg-black z-50 bg-[url('{{asset("image/bg/censusBlack.jpg")}}')] bg-cover bg-center" style="background-image: url('{{asset("image/bg/censusBlack.jpg")}}')">

                        <video id="introVideo" class="max-w-full max-h-full w-auto h-auto object-contain" autoplay muted>
                            <source src="{{ asset('videos/census/newCatch.mp4') }}" type="video/mp4">
                            Tu navegador no soporta videos.
                        </video>

                        <!-- Botón para Saltar Intro -->
                        <button wire:click="hideVideo"
                            class="absolute top-4 right-4 bg-white text-black px-4 py-4 rounded opacity-50 hover:opacity-100 transition">
                            Saltar Intro
                        </button>
                    </div>

                @endif

            </div>

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl h-full">
                <livewire:app.payment.index-component />
            </div>

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl h-full">
                {{-- @include('home.highlighted.point') --}}
                @include('home.highlighted.suspended.point')
            </div>

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl h-full">
                @include('home.highlighted.infoPayment')
            </div>

        </div>

    </x-card>

</div>

@section('scriptsLivewire')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let video = document.getElementById('introVideo');
        video.onended = function () {
            Livewire.dispatch('hideVideo'); // Llama a la función Livewire al terminar
            console.log('close.video');
        };
    });
</script>
@endsection
