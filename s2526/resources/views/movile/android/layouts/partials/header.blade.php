<nav class="navbar navbar-light bg-light shadow-sm pb-2 border-bottom" aria-label="Third navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('movile.android.welcome') }}">
            <span class="text-success fw-bold">Inicio</span>
            <span class="visually-hidden">Icon-only</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsMobile"
            aria-controls="navbarsMobile" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse mt-2 " id="navbarsMobile">
            <ul class="navbar-nav me-auto mb-2 mb-sm-0">

                <li class="nav-item dropdown text-center  border-bottom rounded-0">
                    @include('movile.android.layouts.partials.user')
                </li>

                {{--
                <li class="nav-item text-center  border-bottom rounded-0">
                    <a href="{{ route('movile.android.catchments') }}" class="nav-link {{ Request::is('*catchments*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }}  py-3 " aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Botón de Pago" data-bs-original-title="Botón de Pago">
                        <i class="{{ $icon_menus['catchments'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                        <span>Censo Escolar</span>
                    </a>
                </li>
                --}}

                @auth

                    @admin
                        <li class="nav-item text-center  border-bottom rounded-0">
                            <a href="{{ route('movile.android.admin') }}"
                                class="nav-link {{ Request::is('*admin*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }} py-3  "
                                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Admin"
                                data-bs-original-title="Admin">
                                <i class="{{ $icon_menus['sistema'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                                <span>Admin</span>
                            </a>
                        </li>
                    @endadmin

                    @profesor
                        <li class="nav-item text-center  border-bottom rounded-0">
                            <a href="{{ route('movile.android.profesor') }}"
                                class="nav-link {{ Request::is('*profesor*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }} py-3  "
                                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Profesor"
                                data-bs-original-title="Profesor">
                                <i class="{{ $icon_menus['profesor'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                                <span>Profesor</span>
                            </a>
                        </li>
                    @endprofesor

                    @evaluacion
                        <li class="nav-item text-center  border-bottom rounded-0">
                            <a href="{{ route('movile.android.evaluacion') }}"
                                class="nav-link {{ Request::is('*evaluacion*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }} py-3  "
                                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Profesor"
                                data-bs-original-title="Profesor">
                                <i class="{{ $icon_menus['evaluacion'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                                <span>Coord. Evaluación</span>
                            </a>
                        </li>
                    @endevaluacion

                    @representant
                        <li class="nav-item text-center  border-bottom rounded-0">
                            <a href="{{ route('movile.android.representant') }}"
                                class="nav-link {{ Request::is('*representant*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }} py-3  "
                                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Representante"
                                data-bs-original-title="Representante">
                                <i class="{{ $icon_menus['representante'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                                <span>Representante</span>
                            </a>
                        </li>
                    @endrepresentant

                    @if (auth()->user()->IsDirector())
                        <li class="nav-item text-center  border-bottom rounded-0">
                            <a href="{{ route('movile.android.director') }}"
                                class="nav-link {{ Request::is('*director*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }} py-3  "
                                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Representante"
                                data-bs-original-title="Representante">
                                <i class="{{ $icon_menus['director'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                                <span>Director</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->IsBienestar())
                        <li class="nav-item text-center  border-bottom rounded-0">
                            <a href="{{ route('movile.android.bienestar') }}"
                                class="nav-link {{ Request::is('*bienestar*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }} py-3  "
                                data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Bienestar"
                                data-bs-original-title="Bienestar">
                                <i class="{{ $icon_menus['bienestar'] ?? 'fas fa-heart' }} fa-2x d-block mx-auto mb-1"></i>
                                <span>Bienestar</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item text-center  border-bottom rounded-0">
                        <a href="{{ route('movile.android.poll') }}"
                            class="nav-link {{ Request::is('*poll*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }} py-3 "
                            data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Consultas"
                            data-bs-original-title="Consultas">
                            <i class="{{ $icon_menus['poll'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                            <span>Consultas</span>
                        </a>
                    </li>

                @endauth

                {{--
                <li class="nav-item text-center  border-bottom rounded-0">
                    <a href="{{ route('movile.android.competitions.general')}}" class="nav-link {{ Request::is('*competitions*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }} py-3 " data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Competiciones" data-bs-original-title="Competiciones">
                        <i class="{{ $icon_menus['competitions'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                        <span>Competiciones</span>
                    </a>
                </li>
                --}}

                <li class="nav-item dropdown text-center  border-bottom rounded-0">
                    @include('movile.android.layouts.partials.payments')
                </li>

                <li class="nav-item text-center  border-bottom rounded-0">
                    <a href="{{ route('movile.android.bot') }}"
                        class="nav-link {{ Request::is('*bot*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }}  py-3 "
                        aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right"
                        aria-label="Botón de Pago" data-bs-original-title="Botón de Pago">
                        <div class="d-block mx-auto mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                                class="bi bi-chat-dots" viewBox="0 0 16 16">
                                <path
                                    d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                                <path
                                    d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9.06 9.06 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.437 10.437 0 0 1-.524 2.318l-.003.011a10.722 10.722 0 0 1-.244.637c-.079.186.074.394.273.362a21.673 21.673 0 0 0 .693-.125zm.8-3.108a1 1 0 0 0-.287-.801C1.618 10.83 1 9.468 1 8c0-3.192 3.004-6 7-6s7 2.808 7 6c0 3.193-3.004 6-7 6a8.06 8.06 0 0 1-2.088-.272 1 1 0 0 0-.711.074c-.387.196-1.24.57-2.634.893a10.97 10.97 0 0 0 .398-2z" />
                            </svg>
                        </div>
                        <span>Bot SAEFL</span>
                    </a>
                </li>


            </ul>
        </div>
    </div>
</nav>
