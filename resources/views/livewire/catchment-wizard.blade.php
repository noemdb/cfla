<div class="flex flex-col min-h-screen bg-black lg:flex-row">

    <!-- Estado de carga (Spinner) -->
    <div wire:loading wire:target="sendEmailCode, validateEmailCode, validateStudent, saveEnrollment"
        class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-green-200 p-4 rounded-lg shadow-lg flex flex-col items-center">
            <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-2 text-gray-700 font-semibold">Preparando el siguiente paso...</p>
        </div>
    </div>

    <div style="background-image: url('{{ asset('image/bg/census.jpg') }}')"
        class="absolute inset-0 z-0 h-full w-full bg-[url('{{ asset('image/bg/census.jpg') }}')] bg-cover bg-center opacity-10">
    </div>

    <!-- Estado de carga (Spinner) -->
    <div wire:loading wire:target="sendEmailCode, validateEmailCode, validateStudent, saveEnrollment"
        class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-green-200 p-4 rounded-lg shadow-lg flex flex-col items-center">
            <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-2 text-gray-700 font-semibold">Preparando el siguiente paso...</p>
        </div>
    </div>

    @if ($showVideo)
        <!-- Pantalla de Video -->
        <div class="fixed inset-0 p-4 rounded shadow flex items-center justify-center bg-black z-50 bg-[url('{{ asset('image/bg/censusBlack.jpg') }}')] bg-cover bg-center"
            style="background-image: url('{{ asset('image/bg/censusBlack.jpg') }}')">

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
    @else
        <!-- Left Section -->
        <div class="relative block w-full p-2 sm:p-8 lg:w-1/2 lg:block ">

            <div class="w-full z-10">
                @include('livewire.census.section.left')
            </div>

        </div>

        <!-- Right Section -->
        <div class="flex w-full items-center sm:justify-center lg:justify-start lg:w-1/2 bg-black p-6">

            <div class="w-full z-10 max-w-md rounded-[40px] p-6 lg:p-12">

                @includeWhen($currentStep === 1, 'livewire.census.asistent.stepCi')

                @includeWhen($currentStep === 2 && $wizardFlow === 'A', 'livewire.census.asistent.stepEmail')
                
                @includeWhen($currentStep === 2 && $wizardFlow === 'B', 'livewire.census.asistent.stepList')

                @includeWhen($currentStep === 3, 'livewire.census.asistent.stepStudent')

                @includeWhen($currentStep === 4, 'livewire.census.asistent.stepDownload')

            </div>

            {{-- <x-errors /> --}}

        </div>
    @endif

    {{-- Modal Código de Vestimenta (teleportado al body) --}}
    <template x-teleport="body">
        <div>
            @if ($modalDressCode)
                @include('livewire.census.section.dress-code')
            @endif
        </div>
    </template>

</div>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        let video = document.getElementById('introVideo');

        // When video has loaded enough to play
        video.addEventListener('canplay', function() {
            Livewire.dispatch('videoLoaded');
        });

        // When video ends
        video.onended = function() {
            Livewire.dispatch('hideVideo');
            console.log('close.video');
        };
    });
    </script>
@endsection
