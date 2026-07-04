<div class="card">
    <div class="card-header p-1 m-1 alert-secondary">
        <h6 class="card-title p-1 m-1">
            <b>CREDITOS A FAVOR</b><br>
            @php $total_credito = (!empty($representant->total_credito)) ? $representant->total_credito: 0 ; @endphp
            <span class="badge badge-info">Bs {{f_float($total_credito) ?? '0,00'}}</span>
        </h6>
    </div>
    <div class="card-body">
        @includeWhen($total_credito > 0, 'administracion.registropagos.form.fields.representant.credito')
    </div>
</div>
<div class="card pt-1">
        <div class="card-header p-1 m-1 alert-secondary">
        <h6 class="card-title p-1 m-1">
            <b>ABONOS EN TRANSITO</b><br>
            @php $total_abono = (!empty($representant->total_abono)) ? $representant->total_abono: 0 ; @endphp
            <span class="badge badge-secondary">Bs {{ f_float($total_abono) }}</span>
        </h6>
    </div>
    <div class="card-body">
        @includeWhen($total_abono > 0, 'administracion.registropagos.form.fields.representant.abonos')
    </div>
</div>

<h6>
    <span class="small text-muted"><b>Créditos más Abonos</b></span>
    <span class="badge badge-light">
        {{-- {{ f_float($total_credito + $total_abono) }} --}}
    </span>
</h6>
<h6>
    <span class="small text-muted"><b>Monto Ingreso</b></span>
    <span id="span_ingreso_ammount"  class="badge badge-dark">0,00</span>
</h6>
