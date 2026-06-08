<a class="nav-link dropdown-toggle text-success" href="#" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="fa fa-user  fa-2x d-block mx-auto mb-1" aria-hidden="true"></i>
    <span class="text-success">Usuario</span>
</a>
<ul class="dropdown-menu">
    @auth
        <li><a class="dropdown-item disabled" href="#">Configuración</a></li>
        <li><a class="dropdown-item disabled" href="#">Perfil</a></li>
        <li><a class="dropdown-item disabled" href="#">Notificaciones</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('movile.android.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt text-danger"></i>
                {{ __('Salir') }}
                <form id="logout-form" action="{{ route('movile.android.logout') }}" method="POST" style="display: none;"> @csrf </form>
            </a>
        </li>
    @else
        <li><a class="dropdown-item" href="{{ route('movile.android.welcome') }}">Iniciar sesión</a></li>
    @endauth
</ul>
