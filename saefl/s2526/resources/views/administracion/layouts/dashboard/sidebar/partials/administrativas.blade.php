<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Inscripciones Administrativas">Inscrip. Administrativas</span>
<div class="dropdown-divider mb-0"></div>

<a title="Asistente" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2"
    href="{{ route('administracion.administrativas.asistente') }}">
    <i class="{{ $icon_menus['inscripciones'] }} text-info"></i>
    Asistente
</a>

<a title="Inscripciones Administrativas" class="{{ $class ?? 'nav-link text-success' }} p-1 pl-2"
href="{{ route('administracion.administrativas.asignar') }}">
{{-- href="{{ route('administracion.administrativas.asignar','2') }}"> --}}
    <i class="{{ $icon_menus['inscripciones'] ?? '' }}  text-primary"></i>
    Administrativas
</a>

<a title="Listado (Listado especial con botones de acción) de Inscripciones Administrativas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.administrativas.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Listados (PDF y XLS)
</a>

<a title="Retiros Administrativos" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.administrativas.retiro') }}">
    <i class="{{ $icon_menus['retiro'] ?? '' }}  text-danger"></i>
    R. Administrativos
</a>

<a title="Libro de Inscripciones Administrativas" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.administrativas.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-info"></i>
    Libro
</a>

<a title="Listado de las R. de Matrícula" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.enrollments.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-danger"></i>
    Listado de las R. de Matrícula
</a>

<a title="Estudiantes con prosecución sin Ins.Adm." class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.administrativas.unregistered') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-danger"></i>
    Prosecución sin Ins.Adm.
</a>
