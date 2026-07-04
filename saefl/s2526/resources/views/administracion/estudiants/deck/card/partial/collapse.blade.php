@php
    $grado = ($estudiant->grado) ? : null;
    $class_text_color = ($grado) ? $grado->class_text_color : 'text-secondary';
    $style_blacklist = ($estudiant->status_blacklist=="true" || $representant->status_blacklist=="true") ? " style=text-decoration:line-through; ": null;
    $status_blacklist = ($estudiant->status_blacklist=="true" || $representant->status_blacklist=="true") ? true : false;
    $bad_exchange_ammount_expire_bill = $estudiant->bad_exchange_ammount_expire_bill;
    $planpago = $estudiant->planpago;

    $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill,2);
    $messege_black_list = null;
    if ($status_blacklist=="true") {
        if (round($exchange_ammount_expire_bill,2) > 0) {
            $messege_black_list = "Este estudainte o su representante tiene compromisos pendientes con la administración de la Institución";
        } else {
            $messege_black_list = "Este estudainte o su representante incumplió con el compromiso de pago en las fechas correspondientes";
        }
    }    
@endphp
<a class="collapsed w-100 text-left text-wrap {{ ($exchange_ammount_expire_bill>0 && $exchange_ammount_unexpired_bill>0) ? null : ' disabled ' }}" data-toggle="collapse" href="#id_label-bodycollapse_{{$estudiant->id}}" role="button" aria-expanded="false" aria-controls="idusers_label-bodycollapse" style="text-decoration: none;" title="{{ $messege_black_list ?? null }}">
    {{-- @admon --}}
    <span class="mt-1 p-2 {{ ($planpago) ? 'font-weight-bold' : 'font-weight-light'}} {{$class_text_color}}" {{$style_blacklist ?? null }} >
        {{$estudiant->short_name}}
    </span>

    @if (empty($exchange_rate_current))
        <span class="badge badge-danger mt-1">STDC</span>
    @endif

    <span class=" float-right" >
        @if (round($exchange_ammount_expire_bill,2) > 0) 
            @php $title = ($exchange_rate_current) ? 'Bs. '.f_float($ammount_expire_bill_exchange).' || TDC: '.f_float($exchange_rate_current->ammount) : null @endphp
            <span class="font-weight-bold text-danger m-1 p-1 small" title="{{ $title ?? '' }}" >
                <span class="text-danger">
                    Bs. {{ ($ammount_expire_bill_exchange <> 0) ? readable_int($ammount_expire_bill_exchange) : 'STDC'}}
                </span>
                <span class="text-dark">||</span>
                <span  class="text-dark">USD {{f_float($exchange_ammount_expire_bill)}}</span>
            </span>
        @else
            @if ($planpago)
                @if ($bad_exchange_ammount_expire_bill == 0)
                    <span class="text-success font-weight-bold mt-1 mr-1 float-right {{ $class_text_color }}">SOLVENTE</span>
                @else
                    <span class="badge badge-danger font-weight-bold mt-1 mr-1 float-right">$ {{f_float($bad_exchange_ammount_expire_bill,2)}}</span>
                @endif
            @else
                <h6><span class="badge badge-warning mt-1 font-weight-bold p-1 m-1 float-right {{ $class_text_color }}" title="Sin inscripción administrativa">SIN I.A</span></h6>
            @endif
        @endif
    </span>

</a>
@php /*FixNMDB*/ @endphp
{{-- @admon  --}}
    @if ($exchange_ammount_expire_bill>0 || $exchange_ammount_unexpired_bill>0)
        <div class="collapse" id="id_label-bodycollapse_{{$estudiant->id}}">
            @if($exchange_ammount_expire_bill>0)
                <div class="p-1 m-1 border-0">
                    @include('administracion.estudiants.partial.estudiant_bill_state',['show_ctas' => 'true'])
                </div>
            @endif
            @if($exchange_ammount_unexpired_bill>0)
                <div class="p-1 m-1 border-0">
                    @include('administracion.estudiants.partial.estudiant_unexpired_bill_state',['show_ctas' => 'true'])
                </div>
            @endif
        </div>
    @endif

{{-- @endadmon  --}}
