<div class="card h-100 {{$estudiant->status_active_bank=='false'  ? 'border-danger':''}}">


    <img class="card-img-top" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">
    
    {{-- @isset($estudiant->logo)

    <img alt="{{ $estudiant->logo ?? '' }}" class="card-img-top" src="{{ $estudiant->logo ?? '' }}">    
    @endisset --}}
    
    <div class="card-body p-1">
        <small class="align-text-bottom text-mute">
            {{$estudiant->name.' '.$estudiant->lastname}}<br>
            CI: {{$estudiant->ci_estudiant}}
            {{-- Grado Inicial: {{$estudiant->getGrado($estudiant->grado_inicial_id) ?? ''}} Sección {{$estudiant->seccion_inicial ?? ''}}<br> --}}
        </small>
    </div>

    <span class="text-mute text-center">
        @isset($estudiant->getInscripcion()->id)
            Inscrito en {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}}  {{$estudiant->getInscripcion()->seccion->name ?? ''}}
            {{-- <br> --}}
            {{-- <span class="text-primary">{{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}</span> --}}
        @endisset
    </span>

</div>




