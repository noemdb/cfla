<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Planes benéficos">Planes benéficos</span>
<div class="dropdown-divider mb-0"></div>

<a title="Asignar un plan benéfico a un estudiante" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.configuraciones.plan_beneficos.index') }}">
    <i class="{{ $icon_menus['notificaciones'] ?? '' }}  text-success"></i>
    Asignar
</a>
<a title="Listado (Listado especial con botones de acción) de los credito a favor" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.configuraciones.plan_beneficos.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Listado
</a>