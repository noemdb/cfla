@php
    $name_callout = ($estudiant) ? $estudiant->getInscripcion()->seccion->grado->color : 'default';
@endphp

<div class="bd-callout bd-callout-{{$name_callout}} h-100">

    <div class="card h-100 {{$estudiant->status_active=='false'  ? 'border-secondary':''}}">

        <div class="card-body p-1">
            <span class=" float-right">
                {!! (!empty($estudiant->administrativa)) ? $estudiant->administrativa->planpago->badge : null !!}
            </span>
            <p class=" mb-2 pb-2 border-bottom font-weight-bold">
                {{$estudiant->fullname}} <span class=" text-muted">CI: {{$estudiant->ci_estudiant}}</span>
            </p>

            @foreach ($estudiant->planbeneficos as $planbenefico)

                <ul class="list-group p-2 list-group-flush">
                    <li class="list-group-item font-weight-bold list-group-item-secondary">
                        {{$planbenefico->descuento->descuento_name ?? ''}} {{$planbenefico->descuento->descuento_ammount ?? ''}} %
                    </li>
                    <li class="list-group-item">
                        <span class="font-weight-bold">Observación</span> {{$planbenefico->observations ?? ''}}
                    </li>
                    <li class="list-group-item">
                        <span class="font-weight-bold">Fecha Inicial</span> {{ (!empty(f_date($planbenefico->created_at))) ? f_date($planbenefico->created_at) : null}}
                    </li>
                </ul>

            @endforeach

      </div>
    </div>
  </div>
