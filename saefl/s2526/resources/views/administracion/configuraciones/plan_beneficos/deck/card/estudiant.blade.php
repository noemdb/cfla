<div class="bd-callout bd-callout-{{$estudiant->getInscripcion()->seccion->grado->color ?? 'default'}} h-100">
  <div class="card h-100 {{$estudiant->status_active=='false'  ? 'border-secondary':''}}">
    
    <img class="card-img-top" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">

    <div class="card-body p-1">

      <span class="text-mute small" style="">
        {{$estudiant->fullname}}<br>
        CI: {{$estudiant->ci_estudiant}}
        <span class=" float-right">
            {!! (!empty($estudiant->administrativa)) ? $estudiant->administrativa->planpago->badge : null !!}
        </span>
      </span>

      @foreach ($estudiant->planbeneficos as $planbenefico)
        <dl class="small mb-1 border-top">
            <dt>
              {{$planbenefico->descuento->descuento_name}}
              <span id="credito_a_ammount" class="">
                  {{$planbenefico->descuento->descuento_ammount}}%
              </span>
            </dt>
        </dl>
      @endforeach

    </div>
    
    <div class="p-2 m-2 border rounded">

      <a title="Asignar Plan Benéfico" class="btn btn-success btn-sm btn-block mb-2 border-bottom" href="{{ route('administracion.configuraciones.plan_beneficos.create',$estudiant->id) }}" role="button">
          Asignar
      </a>

      @if ( !empty($estudiant->planbeneficos->first()) )
          <div class="btn-group w-100" role="group" aria-label="">
              @foreach ($estudiant->planbeneficos as $planbenefico)
                  <a title="Asignar Plan Benéfico [{{$loop->iteration}}]" class="btn btn-warning btn-sm" href="{{ route('administracion.configuraciones.plan_beneficos.edit',$planbenefico->id) }}" role="button">
                      <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                      <span class="badge badge-light float-right">{{ $loop->iteration ?? '' }} </span>
                  </a>
              @endforeach
          </div>
      @endif

    </div>

    

  </div>
</div>
