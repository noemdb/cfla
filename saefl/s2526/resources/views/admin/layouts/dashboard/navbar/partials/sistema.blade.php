<a title="Sistema" class="nav-link btn btn-success dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon_menus['sistema'] }} text-light"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">
    <a class="dropdown-item" href="{{ route('admin.home')}}" title="Dashboard">
        <i class="{{ $icon_menus['dashboard'] }} text-primary"></i>
        Panel
    </a>
    <a class="dropdown-item" href="#">
        <i class="{{ $icon_menus['config'] }} text-dark"></i>
        Configuraciones
    </a>
    {{-- <div class="dropdown-divider mb-0"></div>  
    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt text-danger"></i>
        {{ __('Salir') }}
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </a> --}}
</div>





