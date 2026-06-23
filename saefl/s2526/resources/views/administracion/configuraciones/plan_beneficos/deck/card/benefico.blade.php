<div class="bd-callout bd-callout-{{$estudiant->getInscripcion()->seccion->grado->color ?? 'default'}} h-100">
    <div class="card h-100 {{$estudiant->status_active=='false'  ? 'border-danger':''}}">

        <img class="card-img-top" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">

        <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
                {{$estudiant->name.' '.$estudiant->lastname}}<br>
                CI: {{$estudiant->ci_estudiant}}<br>
                {{$estudiant->full_inscripcion ?? ''}}
            </small>
            @foreach ($estudiant->planbeneficos as $planbenefico)
                <dl class="mb-1">
                    <dt>{{$planbenefico->descuento->descuento_name ?? ''}}</dt>
                    <dd>
                        <span id="credito_a_ammount" class="">
                            Monto: {{$planbenefico->descuento->descuento_ammount ?? ''}}
                        </span>
                    </dd>
                </dl>
            @endforeach
            <div class="card-footer p-1">
                @php $ammount_expire_bill_exchange = $estudiant->ammount_expire_bill_exchange @endphp
                @include('administracion.estudiants.partial.estudiant_bill_state',['show_ctas' => 'false'])
            </div>
        </div>

    </div>
</div>




