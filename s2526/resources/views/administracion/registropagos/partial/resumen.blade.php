<div class="card p-0 h-100 bg-light">
    <div class="card-header p-1">
        <p class="font-weight-bold p-0 m-0 text-right">
            Concepto por pagar:<br>
            <span class="text-{{($cuentaxpagar->StateExpireBill($estudiant->id)) ? 'danger':'success'}}">{{$cuentaxpagar->name ?? ''}}</span><br>
            <span class="badge badge-light">Vencimiento: {{f_date($cuentaxpagar->date_expiration,'/') ?? ''}}</span>
        </p>
    </div>
    <div class="card-body p-1">
        <dl class="mb-1">
            <dt>Monto de la transacción</dt>
            <dd>Bs <span id="span_ingreso_ammount" class="badge badge-light">0,00</span></dd>
        </dl>

        {{-- <dl class="mb-1">
            <dt>Créditos Seleccionados</dt>
            <dd>Bs
                <span id="credito_a_ammount" class="badge badge-light">
                    0.00
                </span>
            </dd>
        </dl>

        <dl class="mb-1">
            <dt>Conceptos Seleccionados</dt>
            <dd>Bs
                <span id="credito_a_ammount" class="badge badge-light">
                    0.00
                </span>
            </dd>
        </dl> --}}

        <div class="dropdown-divider mb-0"></div>

        <dl class="mb-1">
            <dt title="Total pagado mas crédito a favor">Pagado</dt>
            <dd>Bs
                <span id="pagado_concepto_ammount" class="badge badge-light">
                    {{ f_float($total_pagado) ?? '0.00'}}
                </span>
            </dd>
        </dl>

        <dl class="mb-1">
            <dt>Total Cuentas</dt>
            <dd>Bs
                <span id="total_concepto_ammount" class="badge badge-light">
                    {{f_float($cuentaxpagar->SumaConceptos('')) ?? 'fallo'}}
                </span>
            </dd>
        </dl>

        @if ($cuentaxpagar->cta_con_descuento)
            <dl class="mb-1">
                <dt>Plan Benéfico (Descuento %)</dt>
                <dd>
                    <span id="descuento_ammount" class="badge badge-light">
                        {{f_float($estudiant->descuento_ammount($cuentaxpagar->id))  ?? 'fallo'}}
                    </span>%
                </dd>
            </dl>
        @endif

        <dl class="mb-1">
            <dt title="Total conceptos menos descuentos">Total Cuentas - Descuentos</dt>
            <dd>Bs
                <span id="credito_ammount" class="badge badge-light">
                    {{f_float($total_concepto_descuento ?? 'fallo')}}
                </span>
            </dd>
        </dl>

        <div class="dropdown-divider mb-0"></div>

        <dl class="mb-1">
            <dt>Total a pagar</dt>
            <dd>Bs
                <span id="total_a_pagar" class="badge badge-light {{($total_a_pagar == 0)? 'text text-success':'text text-danger'}}">
                    {{f_float($total_a_pagar ?? 'fallo')}}
                </span>
            </dd>
        </dl>

        <a class="dropdown-item" href="#"></a>

        {{-- <dl class="mb-1">
            <dt>Monto a crédito a favor</dt>
            <dd>Bs
                <span id="total_a_pagar" class="badge badge-light">
                    0.00
                </span>
            </dd>
        </dl>         --}}
    </div>
</div>









{{-- <dt >Pagado</dt>
    <dd>Bs
        <span id="credito_ammount" class="badge badge-light">
            {{f_float($cuentaxpagar->ConceptosPagados($estudiant->id)->sum('concepto_ammount'))}}
</span>
</dd>
</dl> --}}
