<div class="container flex-grow-1 flex-shrink-0 py-2">

    <div class=" bd-callout bd-callout-{{ ($status_payment_success) ? 'teal' : 'warning'}} mb-2 p-4 bg-white shadow-sm position-relative">

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

            <div id="stepper1" class="bs-stepper" >
                <div class="bs-stepper-header" role="tablist">
                    <div class="step">
                        {{-- <button type="button" wire:click="goStep(4)" class="step-trigger"> --}}
                        <button type="button" class="step-trigger">
                            <span class="{{ ($step==4) ? 'bg-primary': null }} bs-stepper-circle ">4</span>
                            <span class="bs-stepper-label">Código</span>
                        </button>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step">
                        {{-- <button wire:click="goStep(5)" type="button" class="step-trigger"> --}}
                        <button type="button" class="step-trigger">
                            <span class="{{ ($step==5) ? 'bg-primary': null }} bs-stepper-circle">5</span>
                            <span class="bs-stepper-label">Pago</span>
                        </button>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step">
                        {{-- <button wire:click="goStep(6)" type="button" class="step-trigger" role="tab"> --}}
                        <button type="button" class="step-trigger" role="tab">
                            <span class="{{ ($step==6) ? 'bg-primary': null }} bs-stepper-circle">6</span>
                            <span class="bs-stepper-label">Resultado</span>
                        </button>
                    </div>
                </div>

                @if ($step==4)
                    @include('livewire.service.payment.button.credicard.steper.tokenBank')
                @endif

                @if ($step==5)
                    @include('livewire.service.payment.button.credicard.steper.payment')
                @endif

                @if ($step==6)
                    @include('livewire.service.payment.button.credicard.steper.resumen')
                @endif

                @include('livewire.service.payment.button.credicard.partials.errors')

                <hr class="py-0">

                <div class="continer bd-callout bd-callout-warning mb-0 p-2 alert alert-warning py-2" style="max-width: 440xp !important">

                    <div class="d-flex justify-content-center" >
                        <div class="me-auto ps-2 text-center">
                            <div class="d-flex justify-content-center" >
                                <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset('images/brand/bancos/credicarPgoSM.png') }}" alt="" width="100rem">
                            </div>
                        </div>

                        @if ($bank_thumbnail)
                            <div class="ps-2 text-center">
                                <div class="d-flex justify-content-center" >
                                    <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset($bank_thumbnail) }}" alt="" width="100rem">
                                </div>
                            </div>
                        @endif

                        @if ($card_emitter_thumbnail)
                            <div class="ps-2 text-center">
                                <div class="d-flex justify-content-center" >
                                    <img class="border border-light shadow-sm rounded img-fluid p-1 bg-light" src="{{ asset($card_emitter_thumbnail) }}" alt="" width="100rem">
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
