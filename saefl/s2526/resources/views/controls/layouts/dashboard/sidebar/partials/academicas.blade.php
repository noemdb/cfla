<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Inscripciones Académicas">Inscrip. Académicas</span>
<div class="dropdown-divider mb-0"></div>

<a title="Búsqueda de Inscripciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.inscripciones.index') }}">
    <i class="{{ $icon_menus['buscar'] ?? '' }}  text-primary"></i>
    Buscar
</a>

<a title="Inscripción individual" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.inscripciones.individual') }}">
    <i class="{{ $icon_menus['user'] }} text-success"></i>
    Individual
</a>
    
<a title="Libro de Inscripciones Académicas" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.inscripciones.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-info"></i>
    Libro
</a>

<a title="Listado de inscripciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.inscripciones.list.view') }}">
    <i class="{{ $icon_menus['documento'] ?? '' }}  text-primary"></i>
    Listado
</a>

<a title="Listado de inscripciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.inscripciones.matricula.inicial') }}">
    <i class="{{ $icon_menus['documento'] ?? '' }}  text-info"></i>
    Matrícula Inicial
</a>