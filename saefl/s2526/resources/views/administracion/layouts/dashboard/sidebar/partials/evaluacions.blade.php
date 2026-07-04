<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Evaluación">Evaluciones</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Agregar Evaluaciones" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.evaluacions.index') }}">
    <i class="{{ $icon_menus['carga'] ?? '' }}  text-primary"></i>
    Asignación
</a>

<a title="Listado de las Evaluaciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.evaluacions.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado
</a>
