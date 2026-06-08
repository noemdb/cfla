<div class="d-flex justify-content-center align-item-center my-auto h-100 p-2">

    {{-- <div class="jumbotron my-auto text-center mb-0 alert-light border rounded"  style="min-height: 20rem !important; max-width: 20rem !important;"> --}}
    <div class="jumbotron my-auto text-center mb-0 alert-light border rounded h-100" style="max-height: 12rem !important; max-width: 12rem !important;">
        {{-- <span class="small font-weight-bold text-uppercase">DEUDA</span> --}}
        <div class="d-block mb-1 text-danger" style="font-size: 2rem !important">
            @php $late_payment = $representant->late_payment; @endphp
            @php $indice = round( (100 * $late_payment) , 1)@endphp
            <span class=" font-weight-bold">{{$indice ?? ''}}</span><span class="small">%</span>
            {{-- 50% --}}
        </div>
        <span class="font-weight-lighter text-danger" style="font-size: 0.8rem !important">
            Índice de Morosidad
        </span>
    </div>

</div>
