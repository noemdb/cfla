<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-0" title="Inscripciones Administrativas">Profesores</span>
<div class="dropdown-divider mb-0"></div>

<a title="Plan de Evaluación" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2"
href="{{ route('administracion.pevaluacions.index') }}">
    <i class="{{ $icon_menus['pevaluacion'] ?? '' }}  text-primary"></i>
    Plan de Evaluación
</a>

<a title="Evaluaciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
href="{{ route('administracion.evaluacions.index') }}">
    <i class="{{ $icon_menus['evaluacion'] ?? '' }}  text-info"></i>
    Evaluaciones
</a>
<a title="Registro de notas" class="{{ $class ?? 'nav-link text-success' }} p-1 pl-2"
href="{{ route('administracion.boletins.index') }}">
    <i class="{{ $icon_menus['notas'] ?? '' }}  text-primary"></i>
    Registro de notas
</a>

{{-- <span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-0" title="Boletin de Notas">Boletines</span>
<div class="dropdown-divider mb-0"></div> --}}

<a title="Listado de inscripciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="#">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-info"></i>
    Boletines
</a>
