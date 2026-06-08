@php

    $color = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->seccion->grado->color : null ;
    $class_callout = "bd-callout bd-callout bd-callout-".$color;

    $ammount_expire_bill = $estudiant->ammount_expire_bill;
    $class_bill = ($ammount_expire_bill>0) ? 'danger' : 'success' ;
    $border_class = "border border-".$class_bill." rounded-bottom rounded-sm border-top-0 border-right-0 border-left-0";
@endphp

<div class="{{$class_callout ?? 'default'}} h-100 ">
    <div class="card h-100 {{ $border_class ?? ''}}">

        <img class="card-img-top" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">

        <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
                {{$estudiant->name.' '.$estudiant->lastname}}<br>
                CI: {{$estudiant->ci_estudiant}}<br>
                <hr class=" p-0 m-0">
                {{$estudiant->full_inscripcion ?? ''}}<br>
                PROMEDIO: {{$estudiant->promedio ?? ''}}
            </small>

        </div>

    </div>
</div>




