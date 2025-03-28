<div class="flex flex-col min-h-screen bg-black lg:flex-row">

    <!-- Left Section -->
    <div class="relative block w-full p-8 lg:w-1/2 lg:block ">
        @include('livewire.census.section.left')    
    </div>

    <!-- Right Section -->
    <div class="flex w-full items-center sm:justify-center lg:justify-start lg:w-1/2 bg-black p-6">

        <div class="w-full max-w-md rounded-[40px] p-6 lg:p-12">

            @php $show = ($currentStep === 1) ? true : false @endphp
            @includeWhen($show, 'livewire.census.asistent.stepOne')

            @php $show = ($currentStep === 2) ? true : false @endphp
            @includeWhen($show, 'livewire.census.asistent.stepTwo')

            @php $show = ($currentStep === 3) ? true : false @endphp
            @includeWhen($show, 'livewire.census.asistent.stepThree')

            @php $show = ($currentStep === 4) ? true : false @endphp
            @includeWhen($show, 'livewire.census.asistent.stepFour')

        </div>

        <x-errors />

    </div>

</div>