<div class="flex flex-col min-h-screen bg-black lg:flex-row">

    <div style="background-image: url('{{asset("image/bg/census.jpg")}}')" class="absolute inset-0 z-0 h-full w-full bg-[url('{{asset("image/bg/census.jpg")}}')] bg-cover bg-center opacity-10"></div>

    <!-- Estado de carga (Spinner) -->
    <div wire:loading wire:target="sendEmailCode, validateEmailCode, validateStudent, saveEnrollment" 
         class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
        <div class="bg-green-200 p-4 rounded-lg shadow-lg flex flex-col items-center">
            <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-2 text-gray-700 font-semibold">Preparando el siguiente paso...</p>
        </div>
    </div>
    
    <!-- Left Section -->
    <div class="relative block w-full p-2 sm:p-8 lg:w-1/2 lg:block ">

        {{-- <div class="absolute inset-0 z-0 h-full w-full bg-[url('{{asset("image/bg/census.jpg")}}')] bg-cover bg-center opacity-10"></div> --}}

        <div class="w-full z-10">
            @include('livewire.census.section.left')    
        </div>
        
    </div>

    <!-- Right Section -->
    <div class="flex w-full items-center sm:justify-center lg:justify-start lg:w-1/2 bg-black p-6">

        

        <div class="w-full z-10 max-w-md rounded-[40px] p-6 lg:p-12">

            @php $show = ($currentStep === 1) ? true : false @endphp
            @includeWhen($show, 'livewire.census.asistent.stepOne')

            @php $show = ($currentStep === 2) ? true : false @endphp
            @includeWhen($show, 'livewire.census.asistent.stepTwo')

            @php $show = ($currentStep === 3) ? true : false @endphp
            @includeWhen($show, 'livewire.census.asistent.stepThree')

            @php $show = ($currentStep === 4) ? true : false @endphp
            @includeWhen($show, 'livewire.census.asistent.stepFour')

        </div>

        {{-- <x-errors /> --}}

    </div>

</div>