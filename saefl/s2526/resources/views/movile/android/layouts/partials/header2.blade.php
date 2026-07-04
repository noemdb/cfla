

<nav class="navbar navbar-expand-sm bg-body-tertiary pt-1 pb-1 shadow-sm">

    <div class="container-fluid">

        <a href="{{ route('movile.android.welcome') }}" class="navbar-brand p-0" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Icon-only">
            <span class="text-success fw-bold">Inicio</span>
            <span class="visually-hidden">Icon-only</span>
        </a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('movile.android.payment') }}"
                        class="nav-link {{ Request::is('*payment*') ? 'active bg-success text-light' : 'text-success' }}  py-3 border-bottom rounded-0"
                        aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Botón de Pago"
                        data-bs-original-title="Botón de Pago">
                        <i class="{{ $icon_menus['payment'] ?? '' }} fa-2x bi d-block mx-auto mb-1"></i>
                        <span>Botón de Pago</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('movile.android.representant') }}"
                        class="nav-link  {{ Request::is('*representant*') ? 'active bg-success text-light' : 'text-success' }} py-3 border-bottom rounded-0 "
                        data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Representante"
                        data-bs-original-title="Representante">
                        <i class="{{ $icon_menus['representante'] ?? '' }} fa-2x bi d-block mx-auto mb-1"></i>
                        <span>Representante</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('movile.android.profesor') }}"
                        class="nav-link {{ Request::is('*profesor*') ? 'active bg-success text-light' : 'text-success' }} py-3 border-bottom rounded-0 "
                        data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Profesor"
                        data-bs-original-title="Profesor">
                        <i class="{{ $icon_menus['profesor'] ?? '' }} fa-2x bi d-block mx-auto mb-1"></i>
                        <span>Profesor</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('movile.android.poll')}}"
                        class="nav-link {{ Request::is('*poll*') ? 'active bg-success text-light' : 'text-success' }} py-3 border-bottom rounded-0"
                        data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Consultas"
                        data-bs-original-title="Consultas">
                        <i class="{{ $icon_menus['poll'] ?? '' }} fa-2x bi d-block mx-auto mb-1"></i>
                        <span>Consultas</span>
                    </a>
                </li>

                <li class="nav-item">
                    @include('movile.android.layouts.partials.user')
                </li>

            </ul>
        </div>



    </div>

</nav>
