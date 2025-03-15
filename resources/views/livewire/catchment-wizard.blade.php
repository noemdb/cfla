<div class="flex flex-col min-h-screen bg-black lg:flex-row">

    <div class="absolute inset-0 z-0 h-full w-full bg-[url('{{asset("image/bg/census.jpg")}}')] bg-cover bg-center opacity-10"></div>

    <!-- Left Section -->
    <div class="relative block w-full p-8 lg:w-1/2 lg:block ">

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