<div class=" pl-1">
    @foreach ($representant->estudiants as $estudiant)

        <span class=" border-bottom small">
            <i class="{{ $icon_menus['estudiante'] ?? '' }} fa-1x text-dark "></i>
            {{ $estudiant->fullname ?? ''}}
        </span>
        <dl class="pl-2">

            {{-- <dt class="font-weight-bolder py-1">DESCUENTOS</dt> --}}

            @php $planbeneficos = $estudiant->planbeneficos @endphp

            @if (!empty($planbeneficos->count()))
                @foreach ($planbeneficos as $planbenefico)
                @php
                    $descuento = $planbenefico->descuento;
                    $ammont = $descuento->descuento_ammount
                @endphp
                <dd class=" pl-1 pb-0">
                    <div class="input-group py-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="{{ $icon_menus['check'] ?? '' }} small"></i>
                                @php $name = 'descuento['.$estudiant->id.']['.$descuento->id.']'; @endphp
                                {{Form::hidden($name,$ammont)}}
                            </div>
                        </div>
                        <div class="form-control">
                            <span class="small"> {{ $descuento->descuento_name }} </span>
                            <span class="small badge badge-light float-right pt-1"> {{ $ammont ?? ''}}%</span>
                        </div>
                    </div>
                </dd>
                @endforeach
            @else
                <span class=" text-muted small">No tiene descuentos</span>
            @endif

        </dl>

    @endforeach
</div>
