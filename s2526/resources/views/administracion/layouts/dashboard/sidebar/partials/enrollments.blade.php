<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1">Renovación de Matrícula</span>
<div class="dropdown-divider mb-0"></div>

<a title="Planes de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.enrollments.index') }}">
    <i class="{{ $icon_menus['enrollments'] ?? '' }} text-info"></i>
    Imprimir formatos
</a>

{{-- <a title="Listado de las R. de Matrícula" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.enrollments.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-danger"></i>
    Listado de las R. de Matrícula
</a> --}}

<a title="Listado de las R. de Matrícula" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.enrollments.pdf.simple') }}" target="_blank">
    <i class="{{ $icon_menus['pdf'] ?? '' }} text-dark"></i>
    Formato simple
</a>

<a title="Token de Solicitud" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.enrollments.token') }}">
    <i class="{{ $icon_menus['enrollments'] ?? '' }} text-dark"></i>
    Token de Solicitud
</a>
