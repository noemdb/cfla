{{-- <div class="p-1 m-1">
    <i class="fas fa-file-alt fa-2x" aria-hidden="true"></i>
    Inscripciones
</div> --}}
<h5 class="text-dark pl-1">Inscripciones</h5>

<a title="Búsqueda de Inscripciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.inscripciones.index') }}">
    <i class="{{ $icon_menus['buscar'] ?? '' }} "></i>
    Buscar
</a>

<a title="Inscripción individual" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.inscripciones.individual') }}">
    <i class="{{ $icon_menus['user'] }}"></i>
    Individual
</a>

{{-- <a title="Inscripción por lotes" class="{{ $class ?? 'nav-link text-secondary' }} p-1 pl-2" href="{{ route('administracion.inscripciones.batchs') }}">
    <i class="{{ $icon_menus['users'] ?? '' }} "></i>
    Por lotes
</a> --}}

{{-- 
<a title="Inscripción por lotes" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.inscripciones.lists') }}">
    <i class="{{ $icon_menus['inscripciones'] ?? '' }} "></i>
    Listados
</a> 
--}}

<a title="Inscripción por lotes" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.inscripciones.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }} "></i>
    Libro
</a>
<a title="Inscripción por lotes" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.inscripciones.list.view') }}">
    <i class="{{ $icon_menus['imprimir'] ?? '' }} "></i>
    Listado
</a>

