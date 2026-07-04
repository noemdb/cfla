<div class="card text-start">
    <div class="card-header p-0">
        <div class=" d-flex justify-content-center border-0 p-1">
            <div class=" px-1">
                <img class=" img-thumbnail m-2" width="100px" height="100px"
                    src="{{ isset($profesor->logo) ? asset($profesor->logo) : asset('images/avatar/user_default.png') }}"
                    alt="Card image cap">
            </div>
        </div>
    </div>
    <div class="card-body p-1">

        <div class="small pl-2 text-muted d-block">
            <div class="ml-1 pl-1">
                <dt>NOMBRE</dt>
                {{$profesor->fullname ?? ''}}
            </div>
            {{-- <div>
                {{$user->email ?? ''}}
            </div> --}}
        </div>

        <hr>

        <div class="small text-muted">
            <div class="ml-1 pl-1">
                <dt>Nombre de usuario.</dt>
                {{$user->username ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>CI</dt>
                {{$profesor->ci_profesor ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>Tipo de facilitador</dt>
                {{$profesor->ti_teacher ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>Fecha de nacimiento</dt>
                {{(!empty($profesor->date_birth)) ? f_date($profesor->date_birth) : ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>Lugar de nacimiento</dt>
                {{$profesor->city_birth ?? ''}}
                {{$profesor->town_hall_birth ?? ''}}
                {{$profesor->state_birth ?? ''}}
                {{$profesor->country_birth ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>Dirección de residencia</dt>
                {{$profesor->dir_address ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>Números de teléfono</dt>
                {{$profesor->phone ?? ''}}
                {{$profesor->cellphone ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>Correo electrónico</dt>
                {{$profesor->email ?? ''}}
            </div>
        </div>

    </div>
</div>




