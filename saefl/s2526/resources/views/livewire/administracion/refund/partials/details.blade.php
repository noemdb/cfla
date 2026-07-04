<!-- filepath: resources/views/refunds/partials/refund_fields.blade.php -->
<ul class="list-group">
    <li class="list-group-item"><strong>Pago Combinado:</strong> {{ $refund->registro_pago_combinado->description ?? null }}</li>
    <li class="list-group-item"><strong>Crédito a Favor:</strong> {{ $refund->credito_a_favor_id ?? 'N/A' }}</li>
    <li class="list-group-item"><strong>Método de Pago:</strong> {{ $refund->metodo_pago->name ?? null }}</li>
    <li class="list-group-item"><strong>Banco:</strong> {{ $refund->banco->name ?? null }}</li>
    <li class="list-group-item"><strong>Representante:</strong> {{ $refund->representant->name }}</li>
    <li class="list-group-item"><strong>Número de la Transacción:</strong> {{ $refund->number_i_pay }}</li>
    <li class="list-group-item"><strong>Fecha de la Transacción:</strong> {{ f_date($refund->date_transaction) }}</li>
    <li class="list-group-item"><strong>Monto de la Devolución:</strong> Bs. {{ f_float($refund->ammount) }}</li>
    <li class="list-group-item"><strong>Monto de la Devolución Cambiaria:</strong> ${{ f_float($refund->ammount_exchange) }}</li>
    <li class="list-group-item"><strong>Observaciones:</strong> {{ $refund->observations }}</li>
    <li class="list-group-item"><strong>Presente en libro de devoluciones:</strong> {{ ($refund->status_return) ? 'SI':'NO' }}</li>
</ul>