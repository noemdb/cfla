<div class="bd-callout bd-callout-{{$inscripcion->seccion->grado->color ?? 'default'}} h-100">
    <div class="card h-100 {{$estudiant->status_active=='false'  ? 'border-danger':''}}">
      @isset($estudiant->logo)
      <img alt="{{ $estudiant->logo ?? '' }}" class="card-img-top" src="{{ $estudiant->logo ?? '' }}">
      @endisset
  
      <div class="card-body p-1">
        <small class="align-text-bottom text-mute">
          {{$estudiant->name.' '.$estudiant->lastname}}<br>
          CI: {{$estudiant->ci_estudiant}}
          {{-- Grado Inicial: {{$estudiant->getGrado($estudiant->grado_inicial_id) ?? ''}} Sección
          {{$estudiant->seccion_inicial ?? ''}}<br> --}}
        </small>
      </div>
      <div class="card-footer p-1">

            @if (!empty($estudiant->abono->id))
              $count_abono =  $estudiant->abono->count();
              $title = '['.$estudiant->abono->count().'Abonos disponibles]';               
            @endif
            
            <div class="input-group nput-group-sm mb-1" title="{{$title ?? ''}}">
              <div class="input-group-prepend">
                <span class="input-group-text p-1" id="basic-addon1"><b>{{$count_abono ?? ''}}</b></span>
              </div>
              <a title="Registrar Abono" class="btn btn-info btn-sm form-control" href="{{ route('administracion.abonos.create',$estudiant->id) }}" role="button">
                  Reg. Abono
              </a>
            </div>
            
        </div>
    </div>
  </div>