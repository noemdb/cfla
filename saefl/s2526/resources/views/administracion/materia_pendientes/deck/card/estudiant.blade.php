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
    @if (empty($estudiant->getInscripcion()->id))
    <div class="card-footer p-1">
      <p class="card-text">
        {{-- inscripciones: {{$estudiant->getInscripcion()->id}} --}}
        <a class="btn btn-info btn-sm btn-block"
          href="{{ route('administracion.inscripciones.create',$estudiant->id) }}" role="button">
          Inscribir
        </a>
      </p>
    </div>
    @else
    <span class="text-mute text-center">
      {{-- Inscrito en {{$estudiant->getInscripcion() ?? ''}} Sección {{$estudiant->getInscripcion()->seccion ?? ''}} --}}
      {{-- <br> --}}
      <span class="text-primary">{{$estudiant->getInscripcion()->seccion->grado->name ?? ''}}
        {{$estudiant->getInscripcion()->seccion->name ?? ''}}</span>
    </span>
    @endif
  </div>
</div>