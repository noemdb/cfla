@php
$ammount_expire_bill = $estudiant->ammount_expire_bill;
$ammount_no_expire_bill = $estudiant->ammount_no_expire_bill;
// $border_class = ($ammount_expire_bill>0) ? 'danger' : 'success' ;
// $border_class = "border border-".$border_class." rounded-bottom rounded-sm border-top-0 border-right-0 border-left-0";
// $color = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->seccion->grado->color : null ;
// $class_callout = "bd-callout bd-callout bd-callout-".$color;
@endphp

@if ($ammount_expire_bill>0)

    <dl class="mb-1">
        <dt>Deudas</dt>
        <dd>
            <dl class="pl-1 mb-0">
                <dd class="text-dark mb-0">Vencida <span class="badge badge-danger float-right">Bs. {{f_float($ammount_expire_bill)}}</span></dd>
                @if (Auth::user()->IsAdmon())
                    {{-- <div class=" dropdown-divider mb-0"></div> --}}
                    <dd class="text-dark mb-0">No vencida: <span class="badge badge-warning float-right">Bs. {{f_float($estudiant->ammount_no_expire_bill)}}</span></dd>
                @endif
            </dl>
        </dd>
    </dl>

    <hr class="my-1">

    {{-- @if (Auth::user()->IsAdmon())     --}}
        {{-- @if (isset($show_ctas) && $show_ctas=='true') --}}
            <dl class="mb-1">
                <dt class="text-dark">Conceptos por cobrar</dt>
                <dd class="pl-1">
                    @include('administracion.registropagos.partial.cta_x_pagar',['show_concet'=>'true'])
                </dd>
            </dl>
        {{-- @endif --}}
    {{-- @endif --}}

@else
    <dl class="mb-1">
        @if ($estudiant->administrativa)
            <span>Estudiante</span> <span class="badge badge-success float-right mt-1">SOLVENTE</span>
        @else
            <small class="text-danger float-right small font-weight-bold">NO TIENE PLAN DE PAGO ASIGNADO</small>
        @endif
        {{-- <dt>Estudiante <span class="badge badge-success float-right">SOLVENTE</span></dt> --}}
    </dl>
@endif

