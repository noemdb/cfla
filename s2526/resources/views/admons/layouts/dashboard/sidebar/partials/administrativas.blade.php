<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Inscripciones Administrativas">Inscrip. Administrativas</span>
<div class="dropdown-divider mb-0"></div>

<a title="Inscripciones Administrativas" class="{{ $class ?? 'nav-link text-success' }} p-1 pl-2" 
href="{{ route('administracion.administrativas.asignar','2') }}">
    <i class="{{ $icon_menus['inscripciones'] ?? '' }}  text-primary"></i>
    Administrativas
</a>
{{-- <a title="Búsqueda de Inscripciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="#">
    <i class="{{ $icon_menus['buscar'] ?? '' }}  text-primary"></i>
    Buscar
</a>

<a title="Inscripción individual" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="#">
    <i class="{{ $icon_menus['user'] }} text-success"></i>
    Individual
</a> --}}

<a title="Listado de Inscripciones PDF" class="{{ $class ?? 'nav-link text-dange' }} p-1 pl-2" href="{{ route('administracion.administrativas.list.view') }}">
    <i class="{{ $icon_menus['pdf'] ?? '' }}  text-danger"></i>
    {{-- <i class="far fa-file-excel"></i> --}}
    Listado PDF
</a> 

<a title="Listado de Inscripciones formato XLS" class="{{ $class ?? 'nav-link text-dange' }} p-1 pl-2" href="{{ route('administracion.administrativas.list.view.excel') }}">
    <i class="{{ $icon_menus['xls'] ?? '' }}  text-success"></i>
    Listado XLS
</a> 

<a title="Listado (Listado especial con botones de acción) de Inscripciones Administrativas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.administrativas.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dange"></i>
    Listado
</a>

<a title="Libro de Inscripciones Administrativas" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" 
    href="{{ route('administracion.administrativas.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-info"></i>
    Libro
</a>

