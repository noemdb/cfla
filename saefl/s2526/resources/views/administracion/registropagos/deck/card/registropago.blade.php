<div class="bd-callout bd-callout-{{$registropago->estudiant->inscripcion->seccion->grado->color ?? 'default'}} h-100">
    <div class="card {{$registropago->status_active=='false'  ? 'border-danger':''}} h-100">
        {{-- <img alt="{{$registropago->logo ?? ''}}" class="card-img-top" src="{{ (isset($registropago->logo)) ? asset($registropago->estudiant->logo) : asset('images/avatar/user_default.png') }}"> --}}
        <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
                {{-- {{$registropago->id ?? ''}} --}}
                {{$registropago->estudiant->name ?? ''}} {{$registropago->estudiant->lastname ?? ''}}<br>
                CI: {{$registropago->estudiant->ci_estudiant ?? ''}}<br>
                {{-- {{$registropago->grados_name ?? ''}} {{$registropago->seccions_name?? ''}} --}}

            </small>
        </div>
        <div class="card-footer text-center p-1 m-1">
            @include('administracion.registropagos.deck.button.registropago')
        </div>
    </div>
</div>