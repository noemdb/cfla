<h4>
    Monto por Cobrar
</h4>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Concepto</th>
            <th>Divisa ($)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Anualidades (Inscripción/Matrícula)</th>
            @php

            @endphp
            {{-- @php $total_annuity_exchange_bill = 939.73; @endphp --}}
            <td>{{ f_float($total_annuity_exchange_bill) }}</td>
        </tr>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Mensualidades (Cuotas)</th>
            {{-- @php $total_monthly_exchange_bill = 29476.77; @endphp --}}
            <td>{{ f_float($total_monthly_exchange_bill) }}</td>
        </tr>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Total</th>
            <td>{{ f_float($total_annuity_exchange_bill + $total_monthly_exchange_bill) }}</td>
        </tr>
    </tbody>
</table>
