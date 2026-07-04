<li class="nav-item p-1">
    <a class="nav-link" title="Administración" href="{{ route('admin.home') }}">
        {{-- <i class="{{ $icon_menus['administracion'] }} fa-2x text-light"></i> --}}
        <i class="fa fa-bookmark" aria-hidden="true"></i>
    </a>
</li>

<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.inicio')
</li>
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.configuraciones')
</li>
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.estudiants')
</li>
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.representants')
</li>
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.pagos_adelantados')
</li>
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.registropagos')
</li>
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.cuentas_cobrar')
</li>
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.operaciones_bancos')
</li>

<li class="nav-item dropdown p-1">
    @include('administracion.layouts.dashboard.navbar.partials.commons')
</li>

<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.usermenu')
</li>