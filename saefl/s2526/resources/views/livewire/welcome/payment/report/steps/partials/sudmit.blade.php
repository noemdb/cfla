<div id="test-l-1" role="tabpanel" class="bs-stepper-pane active" aria-labelledby="stepper1trigger1">



        @if ($status_save)
            <div class="m-2 p-2 border rounded">
                <div class="text-secondary small text-center">Sí quieres reportar otra transacción, haz clic en el siguiente botón.</div>
                <div class="d-flex justify-content-center my-2 py-2 border-top">
                    <button wire:click="goStep(0)" class="btn btn-dark px-1 mx-1">Finalizar</button>
                    <button wire:click="other" class="btn btn-warning px-1 mx-1">Otra transacción</button>
                </div>
            </div>
        @else
            <div class="d-flex justify-content-center mb-3 p-4">
                <div class="small text-muted border rounded p-2">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item my-0 py-1"><b>{{$list_comment['banco_emisor_1']}}:</b> {{$banco_emisor_1}}</li>
                        <li class="list-group-item my-0 py-1"><b>{{$list_comment['banco_id_1']}}:</b> {{$banco_name}}</li>
                        <li class="list-group-item my-0 py-1"><b>{{$list_comment['ammount_1']}}:</b> Bs. {{$ammount_1}}</li>
                        <li class="list-group-item my-0 py-1"><b>{{$list_comment['number_i_pay_1']}}:</b> {{$number_i_pay_1}}</li>
                        <li class="list-group-item my-0 py-1"><b>{{$list_comment['phone_1']}}:</b> {{$phone_1}}</li>
                        <li class="list-group-item my-0 py-1"><b>{{$list_comment['date_transaction_1']}}:</b> {{f_date($date_transaction_1)}}</li>
                    </ul>
                    <div class="d-flex justify-content-center mt-4">
                        <button wire:click="save" class="btn btn-success mx-1 w-75">Registrar</button>
                    </div>
                </div>
            </div>
        @endif



    <div class="d-flex justify-content-evenly mt-3">
        <button wire:click="goStep(1)" class="btn btn-secondary mx-1">Anterior</button>
        <button wire:click="goStep(0)" class="btn btn-dark mx-1">Inicio</button>
    </div>

</div>
