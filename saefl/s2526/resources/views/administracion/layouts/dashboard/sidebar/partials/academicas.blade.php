<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Inscripciones Académicas">Inscrip. Académicas</span>
<div class="dropdown-divider mb-0"></div>

<a title="Renovación de Matrícula" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.enrollments.index') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Renovación de Matrícula
</a>

<a title="Preinscripciones CSV" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.preinscripcions.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Preinscripciones CSV
</a>

<a title="Inscripciones Académicas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.asignar') }}">
    <i class="{{ $icon_menus['documento'] ?? '' }}  text-primary"></i>
    Inscripciones
</a>

<a title="Búsqueda de Inscripciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.index') }}">
    <i class="{{ $icon_menus['buscar'] ?? '' }}  text-info"></i>
    Buscar
</a>

<a title="Inscripción individual" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.individual') }}">
    <i class="{{ $icon_menus['user'] }} text-success"></i>
    Individual
</a>

<a title="Listado de Inscripciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.crud') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Listado
</a>

<a title="Listado de Inscripciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.movement') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Listado de actualizaciones y movimientos
</a>

<a title="Constancia de Prosecución" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.prosecucion') }}">
    <i class="{{ $icon_menus['pdf'] }} text-dark"></i>
    C. de Prosecución
</a>

<a title="Libro de Inscripciones Académicas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-primary"></i>
    Libro Ins. Académicas
</a>

<a title="Libro de Preinscripciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.preinscripcions.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-info"></i>
    Libro de Preinscripciones
</a>

<a title="Listado de inscripciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.list.view') }}">
    <i class="{{ $icon_menus['documento'] ?? '' }}  text-primary"></i>
    Imprimir Listado
</a>

<a title="Listado de inscripciones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.matricula.inicial') }}">
    <i class="{{ $icon_menus['documento'] ?? '' }}  text-dark"></i>
    Matrícula Inicial
</a>

<a title="Retiros Académico de estudiantes" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.retiro') }}">
    <i class="{{ $icon_menus['retiro'] ?? '' }}  text-danger"></i>
    R. Académico
</a>

<a title="Retiros Académico de estudiantes" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.unregistered') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-danger"></i>
    Estudiantes no matrículados
</a>
