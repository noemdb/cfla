<div class="d-flex justify-content-center align-item-center my-auto h-100 p-2">

    @php $meet_payment = $representant->meet_payment; @endphp
    <div class="jumbotron my-auto text-center mb-0 alert-light border rounded h-100" style="max-height: 12rem !important; max-width: 12rem !important;">
        <div class="d-block mb-1 text-info" style="font-size: 2rem !important">
            @php $indice = round( (100 * $meet_payment) , 1)@endphp
            <span class=" font-weight-bold">{{$indice ?? ''}}</span><span class="small">%</span>
        </div>

        <span class="font-weight-lighter text-info" style="font-size: 0.8rem !important">
            Índice de Cumplimiento de Pago
        </span>
    </div>

</div>
