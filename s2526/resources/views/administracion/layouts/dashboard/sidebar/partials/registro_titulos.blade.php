<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Registros Títulos">Promociones - Títulos</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Crear/Editar Promociones" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.registro_titulos.crud') }}">
    <i class="fas fa-graduation-cap fa-1x"></i>
    Promociones
</a>
<a title="Constancia de Promoción / Hoja de Registro de Títulos" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.registro_titulos.index') }}">
    <i class="{{ $icon_menus['registro_titulos'] ?? '' }}"></i>
    Const. Promoción / Reg. Títulos
</a>

<a title="Listado de Títulos registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.titulos.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
    Títulos registrados
</a>

<a title="Listado de Títulos registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.social_actions.index') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
    Horas Comunitarias
</a>
