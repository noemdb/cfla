<h4>
    Monto Cobrado
</h4>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Concepto</th>
            <th>Divisa ($)</th>
            <th>Bolívares (Bs.)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Anualidades (Inscripción/Matrícula)</th>
            <td>{{ f_float($total_annuity_exchange) }}</td>
            <td>{{ f_float($total_annuity_ammunt) }}</td>
        </tr>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Mensualidades (Cuotas)</th>
            <td>{{ f_float($total_monthly_exchange) }}</td>
            <td>{{ f_float($total_monthly_ammunt) }}</td>
        </tr>
        <tr>
            <th class=" pl-4 ml-4" scope="row">Total</th>
            <td>{{ f_float($total_monthly_exchange + $total_annuity_exchange) }}</td>
            <td>{{ f_float($total_monthly_ammunt + $total_annuity_ammunt) }}</td>
        </tr>
    </tbody>
</table>
