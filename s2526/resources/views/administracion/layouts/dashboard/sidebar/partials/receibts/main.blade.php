<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Registro de Abonos">Recibos rápidos</span>
<div class="dropdown-divider mb-0"></div>

<a title="Registrar Abonos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.receibts.recibos.index') }}">
    <i class="{{ $icon_menus['registrar_pago'] }} text-dark"></i>
    Registrar Recibo
</a>
<a title="Listado (Listado especial con botones de acción) de los Abonos registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.receibts.recibos.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dange"></i>
    Listado
</a>
