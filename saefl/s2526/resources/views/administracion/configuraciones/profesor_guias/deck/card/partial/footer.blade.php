@php
    $ammount_expire_bill = $estudiant->ammount_expire_bill;
    $ammount_no_expire_bill = $estudiant->ammount_no_expire_bill;
    $border_class = ($ammount_expire_bill>0) ? 'danger' : 'success' ;
@endphp
<div class="card-footer p-1">
{{-- <div class="card-footer p-1 border-{{$border_class ?? 'light'}} rounded-bottom rounded-sm border-top-0 border-bottom"> --}}
    <a class=" collapsed w-100 text-left text-wrap" data-toggle="collapse" href="#id_label-bodycollapse_{{$estudiant->id}}" role="button" aria-expanded="false" aria-controls="idusers_label-bodycollapse" style="text-decoration: none;">
        @admon
        @if ($ammount_expire_bill>0)
            <span class="badge badge-danger mt-1">Bs. {{f_float($ammount_expire_bill)}}</span>
        @endif

        @if ($ammount_no_expire_bill>0)
            <span class="badge badge-warning mt-1">Bs. {{f_float($ammount_no_expire_bill)}}</span>
        @endif
        @endadmon

        @if (empty($estudiant->administrativa->planpago_id))
            <span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span>                 
        @else
            {!!$estudiant->administrativa->planpago->badge ?? ''!!} 
            @if ($ammount_expire_bill==0)
                <span class="badge badge-success mt-1">SOLVENTE</span>                 
            @endif
        @endif
             
    </a>
    @admon
    @if($ammount_expire_bill<>0)        
        <div class="collapse" id="id_label-bodycollapse_{{$estudiant->id}}">
            <div class="p-1 m-1 border-0">
                @include('administracion.estudiants.partial.estudiant_bill_state',['show_ctas' => 'true'])
            </div>
        </div>
    @endif
    @endadmon   
</div> 