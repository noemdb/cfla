<div>

    <div class=" bd-callout bd-callout-success mb-2 p-4 bg-white shadow-sm position-relative">

        @include('livewire.service.payment.button.credicard.partials.badge.exchange_rate_ammount')

        <div class="position-relative p-0 m-0">
            <div class="position-absolute top-0 end-0">
                <div wire:loading class="p-1">
                    <div class="spinner-border text-warning" role="status"></div>
                </div>
            </div>
        </div>

        <div class="mt-2">

            <h3 class="text-center" style="color:#143D8D"> <span>Asistente</span> </h3>

            @if (Session::has('messenge'))
                <div class="alert alert-warning">
                    {{Session::get('messenge')}}
                </div>
            @endif

            <div id="stepper1" class="bs-stepper">
                <div class="bs-stepper-header" role="tablist">
                    <div class="step">
                        <button wire:click="goStep(1)" type="button" class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
                            <span class="{{ ($step==1) ? 'bg-primary': null }} bs-stepper-circle ">1</span>
                            <span class="bs-stepper-label">Cédula</span>
                        </button>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step">
                        <button wire:click="goStep(2)" type="button" class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                            <span class="{{ ($step==2) ? 'bg-primary': null }} bs-stepper-circle">2</span>
                            <span class="bs-stepper-label">Monto</span>
                        </button>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step">
                        <button wire:click="goStep(3)" type="button" class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                            <span class="{{ ($step==3) ? 'bg-primary': null }} bs-stepper-circle">3</span>
                            <span class="bs-stepper-label">Tarjeta</span>
                        </button>
                    </div>
                </div>

                @if ($step==1)
                    @include('livewire.service.payment.button.credicard.steper.representant')
                @endif

                @if ($step==2)
                    @include('livewire.service.payment.button.credicard.steper.transaction')
                @endif

                @if ($step==3)
                    @include('livewire.service.payment.button.credicard.steper.partials.card')
                    @include('livewire.service.payment.button.credicard.steper.submit')
                @endif

                @include('livewire.service.payment.button.credicard.partials.errors')

            </div>

        </div>

    </div>

</div>



