<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Evaluación">Bienestar Est.</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Correos Electrónicos Automatizados" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.bienestars.index') }}">
    <i class="{{ $icon_menus['options'] ?? '' }}  text-primary"></i>
    Ficha del Estudiante
</a>

<a title="Correos Electrónicos Automatizados" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.bienestars.batch') }}">
    <i class="{{ $icon_menus['pdf'] ?? '' }}  text-dark"></i>
    Imprimir lote
</a>
