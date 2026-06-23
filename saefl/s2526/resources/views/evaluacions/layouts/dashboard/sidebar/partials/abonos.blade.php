<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Registro de Abonos">Registro de Abonos</span>
<div class="dropdown-divider mb-0"></div>

<a title="Registrar Abonos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.abonos.index') }}">
    <i class="{{ $icon_menus['pagos_adelantados'] }} text-success"></i>
    Registrar Abonos
</a>
<a title="Listado (Listado especial con botones de acción) de los Abonos registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.abonos.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dange"></i>
    Listado
</a>