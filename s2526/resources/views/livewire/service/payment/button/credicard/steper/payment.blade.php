<div id="test-l-1" role="tabpanel" class="bs-stepper-pane active text-start" aria-labelledby="stepper1trigger1">

    <div class="text-muted mb-1 text-success py-4 border rounded px-2">
        Monto del pago a procesar:
        <div class="d-flex justify-content-center fs-1 fw-bold pb-0 mb-0">
            Bs {{number_format($ammount_pay,2,'.',',')}}
        </div>
        <div class="text-center">
            @php $ammount_pay_exchange = ($exchange_rate_ammount) ? round(($ammount_pay / $exchange_rate_ammount) , 2) : null; @endphp
            <span class="text-success fw-bold border rounded p-1 mr-2">USD {{number_format($ammount_pay_exchange,2,'.',',')}}</span>
            {{-- <span class=" border rounded p-1 ml-2">TDC {{number_format($exchange_rate_ammount,2,'.',',')}}</span> --}}
        </div>
    </div>

    <div class="text-muted mb-1 text-success">
        {{ $commission_type ?? null}} de la comisión retenida <span class="small">[{{ $ammount_holder_commission}}%]</span> por el uso del servicio Credicard.
        <div class="fw-bold">Monto: Bs {{number_format($commission_amount ,2,'.',',')}} </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        @include('livewire.service.payment.button.credicard.partials.btnGoHome')
        <button wire:click="goStep(4)" class="btn btn-dark mx-1">Anterior</button>
        <button wire:click="goStep(6)" class="btn btn-warning mx-1">Procesar</button>
    </div>

</div>
