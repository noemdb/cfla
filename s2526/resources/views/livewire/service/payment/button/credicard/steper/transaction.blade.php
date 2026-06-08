<div id="test-l-2" role="tabpanel" class="bs-stepper-pane active text-start" aria-labelledby="stepper1trigger2">

    @include('livewire.service.payment.button.credicard.steper.partials.representant')

    @include('livewire.service.payment.button.credicard.steper.partials.ammount_pay')

    <div class="d-flex justify-content-center mt-3">
        @include('livewire.service.payment.button.credicard.partials.btnGoHome')
        <button wire:click="goStep(1)" class="btn btn-dark mx-1">Anterior</button>
        <button wire:click="goStep(3)" class="btn btn-primary mx-1" >Siguiente</button>
    </div>
</div>
