<div class="card h-100 {{$estudiant->status_active_bank=='false'  ? 'border-danger':''}} bd-callout bd-callout-{{$estudiant->getInscripcion()->seccion->grado->color ?? 'default'}}">

    <img class="card-img-top" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">

    <div class="card-body p-1">
        <small class="align-text-bottom text-mute">
            {{$estudiant->name.' '.$estudiant->lastname}}<br>
            CI: {{$estudiant->ci_estudiant}}
            <span class="float-right">
                {!! (!empty($estudiant->administrativa)) ? $estudiant->administrativa->planpago->badge : null !!}
            </span>
            <span class=" d-block border-top font-weight-bold">
            Inscrito en {{$estudiant->fullinscripcion ?? ''}}
            </span>
        </small>
    </div>

</div>




