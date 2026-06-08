<div class="card">
    <div class="card-header p-1 m-1 alert-secondary">
        <h6 class="card-title p-1 m-1">
            <b>DESCUENTOS</b>
        </h6>
    </div>
    <div class="card-body p-0 px-1">
        @foreach ($estudiant->planbeneficos as $planbenefico)
            @php $descuento = $planbenefico->descuento @endphp

            @component('administracion.elements.forms.check')
                @slot('name', 'descuento['.$descuento->id.']')
                @slot('id', 'descuento_id'.$descuento->id)
                @slot('value', 'true')
                @slot('disabled', 'disabled')
                @slot('label', $descuento->descuento_name)
                @slot('badge', $descuento->descuento_ammount.'%'))
            @endcomponent
        @endforeach
    </div>
</div>

<div class="card pt-1 mt-1">
    <div class="card-header p-1 m-1 alert-secondary">
        <h6 class="card-title p-1 m-1">
            <b>CREDITOS A FAVOR..</b>
            @php $total_credito = (!empty($estudiant->total_credito)) ? $estudiant->total_credito: 0 ; @endphp
            <span class="badge badge-info float-right">Bs {{f_float($total_credito) ?? '0,00'}}</span>
        </h6>
    </div>
    <div class="card-body p-0 pl-1 pr-1">
        @includeWhen($total_credito > 0, 'administracion.registropagos.form.fields.estudiant.credito')
    </div>
</div>
<div class="card pt-1 mt-1">
    <div class="card-header p-1 m-1 alert-secondary">
        <h6 class="card-title p-1 m-1">
            <b>ABONOS EN TRANSITO</b>
            @php $total_abono = (!empty($estudiant->total_abono)) ? $estudiant->total_abono: 0 ; @endphp
            <span class="badge badge-secondary float-right">Bs {{ f_float($total_abono) }}</span>
        </h6>
    </div>
    <div class="card-body p-0 pl-1 pr-1">
        @includeWhen($total_abono > 0, 'administracion.registropagos.form.fields.estudiant.abonos')
    </div>
</div>

<p class="d-block">
    <span class="float-right">
        <span class="small text-muted"><b>CAF + ABN.</b></span>
        <span class="badge badge-dark">
            Bs {{ f_float($total_credito + $total_abono) }}
        </span>
    </span>
    <span class="float-left">
        <span class="small text-muted"><b>INGRESO [Bs.]</b></span>
        <span id="span_ingreso_ammount"  class="badge badge-light">0,00</span>
    </span>
</p>
