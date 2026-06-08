<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Planes benéficos">Planes benéficos</span>
<div class="dropdown-divider mb-0"></div>

<a title="Asignar un plan benéfico a un estudiante" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.configuraciones.plan_beneficos.index') }}">
    <i class="{{ $icon_menus['notificaciones'] ?? '' }}  text-success"></i>
    Asignar
</a>
<a title="Listado de los Planes Benéficos registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.configuraciones.plan_beneficos.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
    Listado
</a>
<a title="Listado de los Descuentos registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.configuraciones.descuentos.crud') }}">
    <i class="{{ $icon_menus['descuentos'] ?? '' }}"></i>
    Listado Descuentos
</a>
