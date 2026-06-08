    @php
    $c_registro="";
    $c_ingreso="d-none d-lg-table-cell";
    $c_pagos="";
    $c_creditoaplicados="";
    $c_conceptocancelados="d-none d-md-table-cell";
    $c_descuentoaplicados="d-none d-md-table-cell";
    @endphp

    <table width="100%" class="table table-striped table-hover p-1">
        <thead>
            <tr>
                <th class="{{ $c_registro }}">REGISTRO</th>
                <th class="{{ $c_ingreso }}">TRANSACCIÓN</th>
                <th class="{{ $c_pagos }}">PAGADO</th>
                {{-- <th class="{{ $c_creditoaplicados }}"><strong>C.APLICADO</strong></th> --}}
                <th class="{{ $c_conceptocancelados }}" title="area">CTA.CANCELADA.</th>
                {{-- <th class="{{ $c_descuentoaplicados }}">D.APLICADO</th> --}}
            </tr>
        </thead>

        <tbody id="tdatos">
            <tr>
                <td class="{{ $c_registro  ?? ''}}">
                    @include('administracion.registropagos.show.partial.registro')
                    <hr>
                    <span class=" font-weight-bold">ABONOS APLICADOS</span>
                    @if ($abonos_aplicados->isnotempty())
                    @include('administracion.registropagos.show.partial.abono_aplicados')
                    @endif
                </td>

                <td class="{{ $c_ingreso ?? '' }}">
                    @if (!empty($registropago->pagos))
                    @php $pagos = $registropago->pagos; @endphp
                    @foreach ($pagos as $pago)
                        @php $ingreso = $pago->ingreso; @endphp
                        @includewhen((!empty($ingreso)),'administracion.registropagos.show.partial.ingreso')
                    @endforeach
                    @endif
                </td>

                <td class="{{ $c_pagos ?? '' }}">
                    @if ($registropago->pagos)
                    @php $pagos = $registropago->pagos; @endphp
                    @include('administracion.registropagos.show.partial.pagos')
                    @endif
                    <hr>
                    <span class=" font-weight-bold">CREDITOS APLICADOS</span>
                    @if ($creditos_aplicados->isnotempty())
                    @include('administracion.registropagos.show.partial.creditoaplicados')
                    @endif
                    <hr>
                    <span class=" font-weight-bold">CREDITOS GENERADOS</span>
                    @if (!empty($credito_generado))
                    @include('administracion.registropagos.show.partial.creditoafavor')
                    @endif
                    <hr>
                    <span class=" font-weight-bold">DESCUENTOS APLICADOS [{{$registropago->id}}]</span>
                    @if (!empty($descuentos_aplicados))
                    @php $descuentoaplicados = $registropago->descuentoaplicados; @endphp
                    @include('administracion.registropagos.show.partial.descuentoaplicados')
                    @endif
                </td>

                <td class="{{ $c_conceptocancelados ?? '' }}">
                    @if ($registropago->conceptocancelados)
                    @php
                        $conceptocancelados = $registropago->conceptocancelados;
                    @endphp
                    @include('administracion.registropagos.show.partial.conceptocancelados')
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    @php $pagos = $registropago->pagos; @endphp

    @if ($pagos->IsNotEmpty())



    @foreach ($pagos as $pago)

        @if ($pago->pagos_combinados->isnotempty())

        <hr><hr>
        <span><b>PAGOS COMBINADOS</b></span>

        @php $registropagos = $pago->pagos_combinados; @endphp

        @foreach ($registropagos as $registropago)
            <span> <b> {{$loop->iteration}}</b></span>
            <div class="pl-3">
            @include('administracion.registropagos.show.simple')
            </div>
        @endforeach

        @endif

    @endforeach

    @endif

