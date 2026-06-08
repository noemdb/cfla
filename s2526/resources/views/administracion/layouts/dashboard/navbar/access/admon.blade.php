<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.registropagos')
</li>

@admin
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.isrl')
</li>
@endadmin

<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.administrativas')
</li>

<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.libros.admon')
</li>

@admon
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.assist_controls')
</li>
@endadmon
{{--
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.pagos_adelantados')
</li>
    <li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.cuentas_cobrar')
</li>
<li class="nav-item dropdown pl-1">
    @include('administracion.layouts.dashboard.navbar.partials.operaciones_bancos')
</li>

<li class="nav-item dropdown p-1">
    @include('administracion.layouts.dashboard.navbar.partials.commons')
</li> --}}
