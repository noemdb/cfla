<div class=" bd-callout bd-callout-{{ ($cuentaxpagar->status_edit) ? 'success':'secondary' }}">
    <div class="card p-0 h-100 bg-light">
        <div class="card-header p-1">
            <span class="font-weight-bold p-0 m-0">
                Cuentas de cobro del concepto
                <span class="font-weight-bolder small">[{{$cuentaxpagar->name ?? ''}}]</span>
            </span>
        </div>
        <div class="card-body p-1">
            @if (!empty($cuentaxpagar->conceptopagos->count()))
                @foreach ($cuentaxpagar->conceptopagos as $conceptopago)
                    <dl class="mb-1 border-bottom">
                        <dd class="">
                            <span class=" text-muted">{{$conceptopago->nomconceptopago->name ?? ''}}</span>
                            {{-- <span class="float-right"> --}}
                                <span id="credito_a_ammount" class="badge badge-light">
                                    Bs.{{f_float($conceptopago->concepto_ammount)}}
                                </span>
                            {{-- </span> --}}
                        </dd>
                    </dl>
                @endforeach
                <p class="font-weight-bold pt-2">
                    TOTAL <span class="badge badge-dark float-right">Bs. {{f_float($cuentaxpagar->conceptopagos->sum('concepto_ammount'))}}</span>
                </p>
            @endif
        </div>
        @if (!$cuentaxpagar->status_edit)
            {{-- <hr> --}}
            <small class="text-muted font-weight-bold pt-2">Existen pagos registrados relacionados a ésta cuenta.</small>
        @endif
    </div>
</div>
