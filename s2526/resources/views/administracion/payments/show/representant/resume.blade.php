<ul class="list-group">
    <li class="list-group-item list-group-item-secondary p-1">
        <div class=" font-weight-bold">
            <i class="{{ $icon_menus['representante'] ?? '' }} fa-1x text-dark "></i>
            {{ $representant->name ?? ''}}
            @php $representant_saldo = $representant->ammount_expire_bill @endphp
            @if ($representant_saldo > 0)
                <span class="badge badge-danger float-right">
                    Bs. {{ f_float($representant_saldo) ?? ''}}
                </span>
            @else
                <span class="badge badge-success float-right" title="SOLVENTE">
                    <i class="{{ $icon_menus['check'] }} fa-1x"></i>
                </span>
            @endif
        </div>

    </li>
    
    <li class="list-group-item p-1 small">

        <dl>

            @foreach ($representant->estudiants as $estudiant)
                <dd class=" pl-1 pb-0">

                    <span class=" font-weight-bold">
                        <i class="{{ $icon_menus['estudiante'] ?? '' }} fa-1x text-dark "></i>
                        {{ $estudiant->fullname ?? ''}}
                    </span>

                    @php $estudiant_saldo = $estudiant->ammount_expire_bill @endphp

                    @if ($estudiant_saldo > 0)

                        <dl class="pl-4">

                            <dt class="font-weight-bolder">CONCEPTOS DE COBRO</dt>

                            @php $cuentasxpagars = $estudiant->expire_bill_pendientes @endphp

                            @foreach ($cuentasxpagars as $cuentasxpagar)

                                @php $ammont = $cuentasxpagar->TotalMontoConceptosXPagar($estudiant->id); @endphp

                                @if ($ammont > 0)

                                    <dd class=" pl-1 pb-0">-

                                        <span class=" font-weight-bold">
                                            {{ $cuentasxpagar->name ?? ''}}
                                        </span>

                                        <span class="badge badge-danger float-right">
                                            Bs. {{ f_float($ammont) ?? ''}}
                                        </span>

                                    </dd>

                                @endif

                            @endforeach

                        </dl>

                    @else
                        <span class="badge badge-success float-right" title="SOLVENTE">
                            <i class="{{ $icon_menus['check'] }} fa-1x"></i>
                        </span>
                    @endif
                </dd>
            @endforeach
        </dl>
    </li>

</ul>
