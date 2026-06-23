<div class="dropdown-menu" aria-labelledby="navbarDropdown">
    <a class="dropdown-item" href="#">
        <i class="{{ $icon_menus['config'] }} text-dark"></i>
        Configuraciones
    </a>
    
    <a class="dropdown-item" href="{{ route('dashboard')}}" title="Dashboard">
        <i class="{{ $icon_menus['dashboard'] }} text-warning"></i>
        Dashboard
    </a>

    <div class="dropdown-divider mb-0"></div>

    <a class="dropdown-item" href="{{ route('administracion.home')}}" title="Dashboard">
        <i class="{{ $icon_menus['administracion'] }} text-primary"></i>
        Administración
    </a>
    <a class="dropdown-item" href="{{ route('admin.home')}}" title="Dashboard">
        <i class="{{ $icon_menus['control_estudio'] }} text-info"></i>
        Control de Estudio
    </a>
    <a class="dropdown-item" href="{{ route('admin.home')}}" title="Dashboard">
        <i class="{{ $icon_menus['profesor'] }} text-success"></i>
        Profesores
    </a>
    <a class="dropdown-item" href="{{ route('admin.home')}}" title="Dashboard">
        <i class="{{ $icon_menus['boletin'] }} text-danger"></i>
        Boletín
    </a>

</div>
