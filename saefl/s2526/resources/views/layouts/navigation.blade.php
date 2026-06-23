<nav x-data="{ open: false }" class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('welcome') }}">
            <x-application-logo class="d-block" style="height: 2.25rem; width: auto;" />
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" @click="open = ! open"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Content -->
        <div class="collapse navbar-collapse" id="navbarContent" :class="{'show': open}">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('/') ? 'active' : '' }}"
                        href="{{ route('welcome') }}">
                        {{ __('Inicio') }}
                    </a>
                </li>
                @if(auth()->user()->IsAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}"
                            href="{{ route('activity-logs.index') }}">
                            <i class="fas fa-history me-1"></i>{{ __('Activity Logs') }}
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Settings Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle text-dark text-decoration-none" type="button"
                        id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
