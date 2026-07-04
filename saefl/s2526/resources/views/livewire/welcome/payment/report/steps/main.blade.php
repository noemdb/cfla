<div>

    <div class="mb-2 p-2 bg-white shadow-sm position-relative">

        {{-- @include('livewire.service.payment.button.credicard.partials.badge.exchange_rate_ammount') --}}

        <div class="position-relative p-0 m-0">
            <div class="position-absolute top-0 end-0">
                <div wire:loading class="p-1">
                    <div class="spinner-border text-warning" role="status"></div>
                </div>
            </div>
        </div>

        <div class="mt-2">

            <div id="stepper1" class="bs-stepper">
                <div class="bs-stepper-header" role="tablist">
                    {{-- <div class="step">
                        <button wire:click="goStep(1)" type="button" class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
                            <span class="{{ ($currentStep==1) ? 'bg-primary': null }} bs-stepper-circle ">1</span>
                            <span class="bs-stepper-label">Cédula</span>
                        </button>
                    </div> --}}
                    {{-- <div class="bs-stepper-line"></div> --}}
                    <div class="step">
                        <button wire:click="goStep(1)" type="button" class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                            <span class="{{ ($currentStep==1) ? 'bg-primary': null }} bs-stepper-circle">1</span>
                            <span class="bs-stepper-label">Datos</span>
                        </button>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step">
                        <button wire:click="goStep(2)" type="button" class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                            <span class="{{ ($currentStep==3) ? 'bg-primary': null }} bs-stepper-circle">2</span>
                            <span class="bs-stepper-label">Registro</span>
                        </button>
                    </div>
                </div>

                @if ($currentStep==1)
                    {{-- @include('livewire.welcome.payment.report.steps.partials.representant') --}}
                    @include('livewire.welcome.payment.report.steps.partials.transactions')
                @endif

                @if ($currentStep==2)
                    @include('livewire.welcome.payment.report.steps.partials.sudmit')
                @endif

                {{-- @if ($currentStep==3)
                    @include('livewire.welcome.payment.report.steps.partials.sudmit')
                @endif --}}

                {{-- @include('livewire.service.payment.button.credicard.partials.errors') --}}

            </div>

        </div>

    </div>

</div>
