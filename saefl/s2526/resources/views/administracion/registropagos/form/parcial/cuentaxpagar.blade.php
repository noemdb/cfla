<div class="card">

    <div class="card-body p-1 m-1">

        @if (!empty($estudiant->expire_bills) && count($estudiant->expire_bills)>0)


            <b>CONCEPTOS:</b>
            @foreach ($estudiant->expire_bills as $cuentaxpagar)

                @php $total_a_pagar = $cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id); @endphp

                @if ($cuentaxpagar->ConceptosXPagar($estudiant->id)->count() > 0)

                    @php
                        $conceptos_x_pagar = $cuentaxpagar->ConceptosXPagar($estudiant->id);
                        $conceptos_pagados = $cuentaxpagar->ConceptosPagados($estudiant->id);
                        $monto_descuento = $estudiant->descuento_ammount($cuentaxpagar->id);
                        $descuento = 1 - ($monto_descuento / 100);
                    @endphp

                    <div class="card">
                        @php $id = $cuentaxpagar->name.'_'.$cuentaxpagar->id;  @endphp
                        <div class="card-header alert-light p-0 m-0">
                            @component('administracion.elements.forms.radio')
                                @slot('name', 'cuentaxpagar_id')
                                @slot('id', $id)
                                @slot('value', $cuentaxpagar->id)
                                @slot('label', $cuentaxpagar->name)
                            @endcomponent
                        </div>
                        <div class="card-body m-1 p-1">
                            @include('administracion.registropagos.form.fields.concepto')
                        </div>
                    </div>
                    <div class="dropdown-divider pt-2"></div>
                @endif
            @endforeach
        @else

            <p class="text-danger font-weight-bold text-center pt-2 small">
                No hay Conceptos de Cobro por cancelar
            </p>

        @endif

    </div>
</div>
