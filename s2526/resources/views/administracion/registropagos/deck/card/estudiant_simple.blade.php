<div class="bd-callout bd-callout-{{$estudiant->getInscripcion()->seccion->grado->color ?? 'default'}} h-100">
    <div class="card h-100 {{$estudiant->status_active_bank=='false'  ? 'border-danger':''}}">
        <img class="card-img-top"
            src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}"
            alt="Card image cap">

        <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
                {{$estudiant->name.' '.$estudiant->lastname}}<br>
                CI: {{$estudiant->ci_estudiant}}
            </small>

            <div class="dropdown-divider mb-0"></div>

            @include('administracion.registropagos.partial.estudiant_bill_state')


        </div>

    </div>
</div>
