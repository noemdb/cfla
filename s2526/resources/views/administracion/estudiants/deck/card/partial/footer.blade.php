@php $exchange_ammount_expire_bill = round($exchange_ammount_expire_bill,2); @endphp

<div class="card-footer p-1">
    <a class=" collapsed w-100 text-left text-wrap" data-toggle="collapse" href="#id_label-bodycollapse_{{$estudiant->id}}" role="button" aria-expanded="false" aria-controls="idusers_label-bodycollapse" style="text-decoration: none;">
        {{-- @admon --}}
            @if ($ammount_expire_bill_exchange>0)
                <span class="badge badge-danger mt-1 p-2" title="{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount) : null }}">
                    Bs {{f_float($ammount_expire_bill_exchange)}}
                </span>
            @endif

            @if (empty($exchange_rate_current))
                <span class="badge badge-danger mt-1">STDC</span>
            @endif

            @if ($exchange_ammount_expire_bill>0)
                <span class="badge badge-dark mt-1 p-1">$ {{f_float($exchange_ammount_expire_bill)}}</span>
            @else
                <span class="font-weight-bold text-dark pl-2">Ver detalles <span>{{$exchange_ammount_expire_bill}}</span></span>
            @endif
        {{-- @endadmon --}}

        <span class="float-right">
            @if ($estudiant->planpago)
                @admon <span class="">{!!$estudiant->administrativa->planpago->badge ?? ''!!}</span> @endadmon
                @if ($exchange_ammount_expire_bill<=0) <span class="badge badge-success mt-1">SOLVENTE</span> @endif
            @else
                @admon<span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span> @endadmon
            @endif
        </span>

    </a>
    @php /*FixNMDB*/ @endphp
    {{-- @admon  --}}
        <div class="collapse" id="id_label-bodycollapse_{{$estudiant->id}}">
            @if($exchange_ammount_expire_bill>0)
                <div class="p-1 m-1 border-0 small">
                    @include('administracion.estudiants.partial.estudiant_bill_state',['show_ctas' => 'true'])
                </div>
            @endif
            @if($exchange_ammount_unexpired_bill>0)
                <div class="p-1 m-1 border-0 small">
                    @include('administracion.estudiants.partial.estudiant_unexpired_bill_state',['show_ctas' => 'true'])
                </div>
            @endif
        </div>
    {{-- @endadmon  --}}
</div>
