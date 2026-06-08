<div class="bd-callout bd-callout-{{$estudiant->getInscripcion()->seccion->grado->color ?? 'default'}} h-100">
  <div class="card h-100 {{$estudiant->status_active=='false'  ? 'border-danger':''}}">
    <div class="card-body p-1">
      <small class="align-text-bottom text-mute">
        {{$estudiant->fullname}}<br>
        CI: {{$estudiant->ci_estudiant}}
      </small>
      @include('administracion.estudiants.deck.button.estudiant')
    </div>
  </div>
</div>
