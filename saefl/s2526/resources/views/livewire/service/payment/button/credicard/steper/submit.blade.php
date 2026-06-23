<div id="test-l-3" role="tabpanel" class="bs-stepper-pane active text-center" aria-labelledby="stepper1trigger3">
	<div class="bd-callout bd-callout-warning alert alert-warning py-2">

	    <div class="small text-muted fw-bold">Procesado por:</div>
	    {{-- <img class="img-fluid" src="{{ asset('images/brand/bancos/logo-ccr-sm.png') }}" alt="" width="164" height="36" > --}}
	    <img class="img-fluid" src="{{ asset('images/brand/bancos/logo-ccr.png') }}" alt="" width="164" height="36" >

	</div>

    <hr class="my-0 py-0">

    <div class="d-flex justify-content-center mt-3">
        @include('livewire.service.payment.button.credicard.partials.btnGoHome')
        <button wire:click="goStep(2)" class="btn btn-dark mx-1">Anterior</button>
        <button wire:click="goStep(4)" type="button" class="btn btn-primary mx-1">Conectar</button>
    </div>
</div>
