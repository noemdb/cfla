<div id="test-l-1" role="tabpanel" class="bs-stepper-pane active text-start" aria-labelledby="stepper1trigger1">

    <ul class="list-group mb-3  list-group-flush">
        <li class="list-group-item disabled">
            <span class="text-success fw-bold"> Se ha conectado exitosamente con el Consorcio Credicard. </span>
        </li>
        <li class="list-group-item disabled">
            <span class="text-muted">  Esta conexión caducará el {{$date_expires->format('d-m-Y h:i:s') ?? null}} </span>
        </li>
        @if ($status_send_token_bank == true)
            <li class="list-group-item disabled">
                <span class="text-primary fw-bold ">Se ha enviado con éxito una solicitud al banco emisor de la tarjeta ingresada.</span>
            </li>
            <li class="list-group-item disabled">
                <span class="fw-bold">El banco emisor enviará un código de autorización al titular.</span>
            </li>
        @endif
    </ul>

    @if ($modeSendTokenBank)
        @if ($status_send_token_bank == false)
            <div class="d-flex justify-content-center">
                <div class=" text-center border border-top p-2 rounded w-100">
                    <button wire:click="sendTokenBankRequest()" class="btn btn-warning fw-bold mx-1 w-50 shadow-sm">Enviar</button>
                    <div for="token_bank" class=" fw-bold text-muted small">Envía una solicitud de código de autorización<br>al banco emisor de la tarjeta ingresada.</div>
                </div>
            </div>
        @else
            <label for="token_bank" class=" fw-bold">
                Ingrese código de autorización del banco.
                <div class="text-danger text-end small fw-bold">{{ $modeTest == true ? 'Código de autorización de prueba' : null }}</div>
            </label>
            <input type="number" wire:model.defer="token_bank" id="token_bank" class="form-control" placeholder="Ingrese código">
            @error("token_bank")<span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
        @endif
    @else
        <div class="fw-bold text-secondary">
            La tarjeta ingresada ({{$card_emitter_name ?? null}} ***{{ Str::substr($card_number,-4)}}) no requiere de la solicitud del envío de un código de autorización al titular.
        </div>
    @endif

    <div class="d-flex justify-content-center mt-3">
        @include('livewire.service.payment.button.credicard.partials.btnGoHome')
        <button wire:click="goStep(3)" class="btn btn-dark mx-1">Anterior</button>
        <button wire:click="goStep(5)" {{ ($modeSendTokenBank==true && $status_send_token_bank == false) ? ' disabled ' : null }} class="btn btn-primary mx-1">Siguiente</button>
    </div>

</div>
