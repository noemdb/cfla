
<div class="card  border rounded shadow-sm border border-light">
    <h4 class="card-title py-1 my-1 text-center">
        <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
        <div class="text-center text-muted font-weight-bold">Información importante</div>
    </h4>
    <div class="card-body py-1 my-1 ">
        Su correo electrónico institucional y su contraseña de acceso inicial es:

        <div class="p-2 text-justify text-muted">

            <ul class="list-group">
                <li class="list-group-item px-1">
                    Correo:
                    <div class="pl-1 font-weight-bold ">{{ $user->email ?? '' }}</div>
                </li>
                <li class="list-group-item px-1">
                    Contraseña inicial:
                    <div class="pl-1 font-weight-bold ">frayluis2020</div>
                    <div class="pt-2">
                        <span class="small text-muted float-right">Sí usted ya inició sesión, descarte esta contraseña</span>
                    </div>
                </li>
            </ul>

        </div>

        El cual podrá ser usado para el restablecimiento de sus datos de acceso a nuestros sistemas. Le recomendamos que lo guarde en un sitio seguro.

        <hr>

        <div>
            Puedes ir a tu correo institucional desde el siguiente enlace:
            <p>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" id="btnGroupAddon2">
                            <img class="google-icon" src="{{ asset('images/avatar/social/google.svg') }}" width="24" height="24"/>
                        </div>
                    </div>
                    <a name="" id="" class="form-control btn btn-light" href="https://gmail.com" role="button" target="_blank">
                        <span class="font-weight-bold text-muted">
                            Ingresa a tu cuenta de correo electrónico
                        </span>
                    </a>
                </div>
            </p>
        </div>

    </div>
</div>
