  @php
      $class_callout = "bd-callout bd-callout-".$profesor_guia->grado->color;
      $profesor = $profesor_guia->profesor;
      $grado = $profesor_guia->grado;
      $lapso = $profesor_guia->lapso;
      $seccion = $profesor_guia->seccion;
  @endphp

  <div class="{{$class_callout ?? 'default'}} h-100 ">

    <div class="card h-100 {{$border_class ?? ''}}" style="max-width: 540px;">

      <div class="row no-gutters">

        <div class="col-md-3 pl-1 pt-1 ">
          <h4 class="{{$grado->class_text_color ?? ''}} text-nowrap" style="font-weight:900; font-size:2.2rem" title="{{$grado->name ?? ''}}">
              {{$grado->code_sm ?? ''}}
          </h4>
        </div>

        <div class="col-md-8">
          <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
              <p class="align-text-top small pl-1">
                @admin <span class=" d-block">ID: {{$profesor->id ?? ''}}</span> @endadmin 
                <span> {{$profesor->fullname ?? ''}}<br>CI: {{$profesor->ci_profesor ?? ''}}
              </p>
            </small>
          </div>
        </div>

        @control
        <div class="col-md-1 pr-1 pt-1 pb-1">
          @php $pevaluacions = (empty($profesor->pevaluacions)) ? null : $profesor->pevaluacions; @endphp
          <div class="d-block">
            @if ($pevaluacions)
                <span title="Carga Académica - Planes de Evaluación Aiginados" class="badge badge-light">{{$pevaluacions->count() ?? ''}}</span>
            @endif
          </div>
        </div>
        @endcontrol

      </div>
      <div class="container">
        <div class="row">
          <div class="col-12 text-left px-1">
            <span class="small font-weight-bold text-secondary text-uppercase ">
                Sección {{$seccion->name ?? ''}}
                {{-- {{$lapso->code ?? ''}} --}}
            </span>            
          </div>
          
        </div>
      </div>

    </div>
  </div>
