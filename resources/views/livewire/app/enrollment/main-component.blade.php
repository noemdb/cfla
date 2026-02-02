<div class="flex flex-col min-h-screen bg-black lg:flex-row">

    <!-- Estado de carga (Spinner) -->
    <div wire:loading wire:target="setStart, search, restart"
        class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-emerald-200 p-4 rounded-lg shadow-lg flex flex-col items-center">
            <svg class="animate-spin h-8 w-8 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-2 text-gray-700 font-semibold">Cargando...</p>
        </div>
    </div>

    <div style="background-image: url('{{ asset('image/bg/census.jpg') }}')"
        class="absolute inset-0 z-0 h-full w-full bg-cover bg-center opacity-10">
    </div>

    <!-- Left Section -->
    <div class="relative block w-full p-2 sm:p-8 lg:w-1/2 lg:block ">
        <div class="w-full z-10">
            @include('livewire.app.enrollment.section.left')
        </div>
    </div>

    <!-- Right Section -->
    <div class="flex w-full items-center sm:justify-center lg:justify-start lg:w-1/2 bg-black p-6">
        <div class="w-full z-10 max-w-md rounded-[40px] p-6 lg:p-12">
            @if ($currentStep === 1)
                @include('livewire.app.enrollment.start')
            @elseif($currentStep === 2)
                @include('livewire.app.enrollment.search')
            @endif
        </div>
    </div>

</div>
