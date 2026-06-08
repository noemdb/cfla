

<div class="card h-100 bd-callout bd-callout-{{$estudiant->getInscripcion()->seccion->grado->color ?? 'default'}}">

    <img class="card-img-top"
        src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}"
        alt="Card image cap">

    <div class="card-body p-1">

        <small class="align-text-bottom text-mute d-block">
            {{$estudiant->name ?? ''}} {{$estudiant->lastname ?? ''}}<br>
            {{$estudiant->ci_estudiant ?? ''}}
        </small>

        @isset ($estudiant->getInscripcion()->id)
            <span class="text-mute text-center">
                {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
            </span>
        @endisset

        @if (!empty($estudiant->retiro->id))
            <span class=" d-block">Retiro (Administrativo/Académico) {{$estudiant->created_ap ?? ''}}</span>
        @endif
        {{--
        <div>
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
        </div>
        --}}
    </div>

</div>
