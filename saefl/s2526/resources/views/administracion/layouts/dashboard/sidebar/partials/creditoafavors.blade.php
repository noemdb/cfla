<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Creditos a favor">Creditos a favor</span>
<div class="dropdown-divider mb-0"></div>

{{-- <a title="Registrar Abonos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.abonos.index') }}">
    <i class="{{ $icon_menus['pagos_adelantados'] }} text-success"></i>
    Registrar Abonos
</a> --}}
<a title="Listado (Listado especial con botones de acción) de los credito a favor" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.creditoafavors.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dange"></i>
    Listado
</a>
<a href="{{ route('administracion.creditoafavors.omit') }}" title="Listado (Listado especial con botones de acción) de los credito a favor" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" >
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-primary"></i>
    Omitir CAF
</a>
