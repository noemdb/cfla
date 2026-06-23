<div class="bd-callout bd-callout-{{$inscripcion->seccion->grado->color ?? 'default'}} h-100">
    <div class="card {{$inscripcion->status_active_bank=='false'  ? 'border-danger':''}} h-100">
        {{-- <img alt="{{$inscripcion->logo ?? ''}}" class="card-img-top" src="{{ (isset($inscripcion->logo)) ? asset($inscripcion->estudiant->logo) : asset('images/avatar/user_default.png') }}"> --}}
        <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
                {{-- {{$inscripcion->id ?? ''}} --}}
                {{$inscripcion->name ?? ''}} {{$inscripcion->lastname ?? ''}}<br>
                CI: {{$inscripcion->ci_estudiant ?? ''}}<br>
                {{$inscripcion->grados_name ?? ''}} {{$inscripcion->seccions_name?? ''}}

            </small>
        </div>
        <div class="card-footer text-center p-1 m-1">
            @include('administracion.inscripciones.deck.button.estudiant')
        </div>
    </div>
</div>