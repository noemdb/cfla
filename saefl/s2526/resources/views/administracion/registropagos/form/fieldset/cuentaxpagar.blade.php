<fieldset class="form_fieldset">
    <h6>Paso 2: Cuentas de Cobro.</h6>
    @foreach ($estudiants as $estudiant)

        @php $exchange_expire_bills = $estudiant->exchange_expire_bills @endphp

        {{-- {{ $exchange_expire_bills }} --}}

        <div class="card">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h6 class="card-title">
                    <span class="small">Estudiante: <b>{{$estudiant->fullname ?? ''}}</b></span>
                    @php $descuento_g = (!empty($estudiant->planbeneficos->first())) ? $estudiant->planbeneficos->first():null; @endphp
                    @if ($descuento_g)
                        <span title="A partir de la fecha {{f_date($descuento_g->created_at)}}" class="badge badge-success float-right">{{$descuento_g->descuento->descuento_ammount ?? ''}}%</span>
                    @endif
                </h6>
            </div>
            <div class="card-body p-1 m-1">

                {{-- @if (!empty($estudiant->expire_bills) && count($estudiant->expire_bills)>0) --}}
                @if ($exchange_expire_bills)

                    {{Form::hidden('estudiant_arr['.$estudiant->id.']',$estudiant->id)}}

                    {{-- @foreach ($estudiant->expire_bills as $cuentaxpagar) --}}
                    @foreach ($exchange_expire_bills as $cuentaxpagar)

                        @php $total_a_pagar = $cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id); @endphp

                        @if ($cuentaxpagar->ConceptosPagados($estudiant->id)->count() > 0 or $cuentaxpagar->ConceptosXPagar($estudiant->id)->count() > 0)
                            {{Form::hidden('cuentaxpagar_id['.$estudiant->id.']['.$cuentaxpagar->id.']',$cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id))}}
                            @php
                                $conceptos_x_pagar = $cuentaxpagar->ConceptosXPagar($estudiant->id);
                                $conceptos_pagados = $cuentaxpagar->ConceptosPagados($estudiant->id);
                                $monto_descuento = $estudiant->descuento_ammount($cuentaxpagar->id);
                                $descuento = 1 - ($monto_descuento / 100);
                            @endphp
                            {{-- {{'conceptos_x_pagar: '.$conceptos_x_pagar}}<br> --}}
                            {{-- {{'conceptos_pagados: '.$conceptos_pagados}} --}}
                            <div class="card">
                                <div class="card-body m-1 p-1">
                                    <h6 class="card-title m-0 p-0">
                                        <small class="d-block small">
                                            CONCEPTO: <b>{{$cuentaxpagar->name ?? ''}}</b>
                                            <span class="text-muted small"> Vencimiento: {{f_date($cuentaxpagar->date_expiration,'/') ?? ''}}</span>
                                        </small>
                                    </h6>
                                    @include('administracion.registropagos.form.fields.representant.concepto')
                                    {{-- @include('administracion.registropagos.form.fields.concepto') --}}
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else

                    <p class="text-danger font-weight-bold text-center pt-2 small">
                        No hay Conceptos de Cobro por cancelar
                    </p>

                @endif

            </div>
        </div>
        <div class="dropdown-divider mb-0"></div>
    @endforeach
    <input type="button" name="previous" class="previous-form btn btn-default  w-25 p-0" value="Anterior" />
    <input type="button" name="next" class="next-form btn btn-info w-25 p-0 float-right" value="Siguiente" />
    {{-- <input type="submit" name="submit" class="submit btn btn-success w-25 p-0" value="Guardar" /> --}}
</fieldset>
