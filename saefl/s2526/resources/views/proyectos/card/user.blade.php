@php
    $profesor = (empty($profesor)) ? Auth::user()->profesor : $profesor ;
    $user = Auth::user();
@endphp
<div class="card h-100">

    <img class="pt-1" width="100px" height="100px" src="{{ (isset($profesor->logo)) ? asset($profesor->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">

    <div class="card-body p-1">

        <div class="small pl-2 text-muted d-block">
            <div class="font-weight-bold">
                {{$user->username ?? ''}}
            </div>
            <div>
                {{$user->email ?? ''}}
            </div>
        </div>

        <hr>

        <div class="small text-muted">
            <ol class="ml-1 pl-1">
                <dt>Nombre</dt>
                {{$profesor->fullname ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>CI</dt>
                {{$profesor->ci_profesor ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Tipo de facilitador</dt>
                {{$profesor->ti_teacher ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Fecha de nacimiento</dt>
                {{(!empty($profesor->date_birth)) ? f_date($profesor->date_birth) : ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Lugar de nacimiento</dt>
                {{$profesor->city_birth ?? ''}}
                {{$profesor->town_hall_birth ?? ''}}
                {{$profesor->state_birth ?? ''}}
                {{$profesor->country_birth ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Dirección de residencia</dt>
                {{$profesor->dir_address ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Números de teléfono</dt>
                {{$profesor->phone ?? ''}}
                {{$profesor->cellphone ?? ''}}
            </ol>
            <ol class="ml-1 pl-1">
                <dt>Correo electrónico</dt>
                {{$profesor->email ?? ''}}
            </ol>
        </div>

    </div>

</div>
