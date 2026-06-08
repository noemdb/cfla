<div class="text-right font-weight-bold card bd-callout bd-callout-dark">
    <p class="text-dark">Lista de notificaciones registradas</p>
    <small class="px-1">
        @foreach ($prepagos as $prepago)

            @php $representant = $prepago->representant; @endphp

            <span class=" font-weight-bolder">{{$loop->iteration}}</span>
            <dl class="mb-1 ">
                <dt>Referencia: <span class="text-secondary">{{ $prepago->number_i_pay ?? '' }}</span></dt>
            </dl>
            <dl class="mb-1 ">
                <dt>Monto: <span class="text-secondary">{{ f_float($prepago->ingreso_ammount) }}</span></dt>
            </dl>
            <dl class="mb-1 ">
                <dt>Fecha: <span class="text-secondary">{{ f_date($prepago->date_transaction) }}</span></dt>
            </dl>

            <hr>
        @endforeach
    </small>
</div>
