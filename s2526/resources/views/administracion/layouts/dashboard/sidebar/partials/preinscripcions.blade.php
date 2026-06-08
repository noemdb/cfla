<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Preinscripciones CSV">
    <i class="{{ $icon_menus['incripcions'] ?? '' }} text-success"></i>
    Preinscripciones CSV
</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Procesar Notificaciones de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.preinscripcions.carga.csv') }}">
    <i class="{{ $icon_menus['csv'] ?? '' }} text-success"></i>
    Procesar preinscripciones CSV
</a>

<a title="Listado de las Notificaciones de Pago registradas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.preinscripcions.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
    Listado de las preinscripciones
</a>

<a title="Libro de Preinscripciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.preinscripcions.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-info"></i>
    Libro de Preinscripciones
</a>


<a title="Inscripciones Académicas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.asignar') }}">
    <i class="{{ $icon_menus['documento'] ?? '' }}  text-primary"></i>
    Inscripciones
</a>