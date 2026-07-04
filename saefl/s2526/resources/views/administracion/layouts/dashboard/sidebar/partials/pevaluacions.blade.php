<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Plan de Evaluación">Plan de Evaluación</span>
<div class="dropdown-divider mb-0"></div>
<a title="Asignación de la Carga Académica a los Profesores/Facilitadores" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" 
href="{{ route('administracion.pevaluacions.carga') }}">
    <i class="{{ $icon_menus['carga'] ?? '' }}  text-danger"></i>
    Asignación
</a>
<a title="Indicadores para la sección Plan de Evaluación" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.pevaluacions.index') }}">
    <i class="{{ $icon_menus['chartline'] ?? '' }}  text-dark"></i>
    Indicadores
</a>
<a title="Listado de los Planes de Evaluación" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.pevaluacions.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado
</a>

@include('administracion.layouts.dashboard.sidebar.partials.profesor_gestables')

@include('administracion.layouts.dashboard.sidebar.partials.evaluacions')

@include('administracion.layouts.dashboard.sidebar.partials.boletins')
