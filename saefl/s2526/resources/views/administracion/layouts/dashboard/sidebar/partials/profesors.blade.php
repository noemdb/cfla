<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-0" title="Inscripciones Administrativas">Profesores</span>
<div class="dropdown-divider mb-0"></div>

<a title="Plan de Evaluación" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2"
href="{{ route('administracion.pevaluacions.carga') }}">
    <i class="{{ $icon_menus['pevaluacion'] ?? '' }}  text-primary"></i>
    Plan de Evaluación
</a>

<a title="Evaluaciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
href="{{ route('administracion.evaluacions.crud') }}">
    <i class="{{ $icon_menus['evaluacion'] ?? '' }}  text-info"></i>
    Evaluaciones
</a>

<a title="Carga de notas" class="{{ $class ?? 'nav-link text-success' }} p-1 pl-2 disabled"
{{-- href="{{ route('administracion.boletins.index') }}"> --}}
href="#">
    <i class="{{ $icon_menus['notas'] ?? '' }}  text-primary"></i>
    Carga de notas
</a>

<a title="Evaluaciones Descriptivas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.edescriptivas.index') }}">
    <i class="{{ $icon_menus['edescriptivas'] ?? '' }}  text-dark"></i>
    E. Descriptivas
</a>

{{-- <span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-0" title="Boletin de Notas">Boletines</span>
<div class="dropdown-divider mb-0"></div> --}}

<a title="Listado de Boletines" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.boletin') }}">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-info"></i>
    Boletines
</a>

<a title="Sabana de Notas por Grado/Sección" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.boletins.sabana') }}">
    <i class="{{ $icon_menus['list'] ?? '' }}  text-info"></i>
    Listados de Notas
</a>

@admin
<a title="Materia Pendientes - Diferido" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.materia_pendientes.index') }}">
    <i class="{{ $icon_menus['materia_pendientes'] ?? '' }}  text-info"></i>
    Materia Pendientes
</a>
@endadmin
