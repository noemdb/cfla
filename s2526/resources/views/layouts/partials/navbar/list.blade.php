<a class="dropdown-item" href="#">
    <i class="{{ $icon_menus['config'] }} text-dark"></i>
    Configuraciones
</a>

<a class="dropdown-item" href="{{ route('admin.home')}}" title="Dashboard">
    <i class="{{ $icon_menus['dashboard'] }} text-primary"></i>
    Inicio
</a>

{{--
<a class="dropdown-item" href="{{ route('admin.home') ?? 'home'}}" title="Administracion del Sistema">
    <i class="{{ $icon_menus['administracion'] }} text-secondary"></i>
    Administración
</a>

<a class="dropdown-item" href="{{ route('admin.home') ?? 'home'}}" title="Administracion del Sistema">
    <i class="{{ $icon_menus['control_estudio'] }} text-info"></i>
    Control de Estudios
</a>

<a class="dropdown-item" href="{{ route('poa.home') }}"> 
<a class="dropdown-item" href="{{ route('common.home') ?? 'home' }}" title="Control de Taréas, Mensajes y Alertas">
    <i class="{{ $icon_menus['profesor'] }} text-warning"></i>
    Profesores
</a>

<a class="dropdown-item" href="{{ route('poa.home') }}"> 
<a class="dropdown-item" href="{{ route('expediente.home') ?? 'home' }}" title="Control de Expedientes">
    <i class="{{ $icon_menus['boletin'] }} text-success"></i>
    Boletín Escolar
</a>

<a class="dropdown-item" href="#">
    <i class="fas fa-address-book text-dark"></i>
    Rol
</a>

<a class="dropdown-item" href="#">
    <i class="fas fa-tasks text-warning"></i>
    Actividades
</a>

<a class="dropdown-item" href="#">
    <i class="fas fa-comment text-secondary"></i>
    Mensajes
</a>

--}}


<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <i class="fas fa-sign-out-alt text-danger"></i>
    {{ __('Salir') }}
</a>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
