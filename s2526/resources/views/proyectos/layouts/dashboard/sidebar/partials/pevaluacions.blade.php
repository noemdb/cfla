<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Plan de Evaluación">Plan de Evaluación</span>
<div class="dropdown-divider mb-0"></div>

<a title="Plan de Evaluación" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.pevaluacions.index') }}">
    <i class="{{ $icon_menus['pevaluacion'] ?? '' }}  text-primary"></i>
    Plan de Evaluación
</a>
<a title="Registro de los Planes de Evaluación" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.pevaluacions.carga') }}">
    <i class="{{ $icon_menus['carga'] ?? '' }}  text-danger"></i>
    Registro
</a>
<a title="Listado de los Planes de Evaluación" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.pevaluacions.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado
</a>
{{-- <a title="Listado de las Evaluaciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.evaluacions.crud') }}">
    <i class="{{ $icon_menus['evaluacion'] ?? '' }} text-info"></i>
    Evaluciones
</a> --}}
