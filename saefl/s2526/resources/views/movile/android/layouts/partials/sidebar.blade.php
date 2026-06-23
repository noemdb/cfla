<div class="d-flex flex-column flex-shrink-0 bg-light shadow-sm" style="width: 4rem;">
    <a href="{{ route('movile.android.welcome') }}" class="d-block p-1 link-dark text-decoration-none"
        data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Icon-only">
        <img class="bi pe-none" src="{{ asset('images/brand/144/1.png') }}" alt="" width="54" height="54">
        <span class="visually-hidden">Icon-only</span>
    </a>
    <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
        <li class="nav-item">
            <a href="{{ route('movile.android.payment') }}"
                class="nav-link {{ Request::is('*payment*') ? 'bg-success text-light' : 'text-success' }}  py-3 border-bottom rounded-0"
                aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Botón de Pago"
                data-bs-original-title="Botón de Pago">
                <i class="{{ $icon_menus['payment'] ?? '' }} fa-1x bi pe-none"></i>
            </a>
        </li>
        <li>
            <a href="{{ route('movile.android.representant') }}"
                class="nav-link  {{ Request::is('*representant*') ? 'bg-success text-light' : 'text-success' }} py-3 border-bottom rounded-0 "
                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Representante"
                data-bs-original-title="Representante">
                <i class="{{ $icon_menus['representante'] ?? '' }} fa-1x bi pe-none"></i>
            </a>
        </li>
        <li>
            <a href="{{ route('movile.android.profesor') }}"
                class="nav-link {{ Request::is('*profesor*') ? 'bg-success text-light' : 'text-success' }} py-3 border-bottom rounded-0 "
                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Profesor"
                data-bs-original-title="Profesor">
                <i class="{{ $icon_menus['profesor'] ?? '' }} fa-1x bi pe-none"></i>
            </a>
        </li>
        <li>
            <a href="{{ route('movile.android.poll')}}"
                class="nav-link {{ Request::is('*poll*') ? 'bg-success text-light' : 'text-success' }} py-3 border-bottom rounded-0"
                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Consultas"
                data-bs-original-title="Consultas">
                <i class="{{ $icon_menus['poll'] ?? '' }} fa-1x bi pe-none"></i>
            </a>
        </li>
    </ul>
    <div class="dropdown border-top">
        <a href="#"
            class="d-flex align-items-center justify-content-center p-3 link-dark text-decoration-none dropdown-toggle"
            data-bs-toggle="dropdown" aria-expanded="false">
            <div class="rounded-circle"><i class="{{ $icon_menus['user'] ?? '' }} fa-1x"></i></div>
        </a>
        <ul class="dropdown-menu text-small shadow">
            @auth
                <li><a class="dropdown-item disabled" href="#">Configuración</a></li>
                <li><a class="dropdown-item disabled" href="#">Perfil</a></li>
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
    </div>
</div>
