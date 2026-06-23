<dl class="mb-1 pl-1">
    <dt>Inscricpciones</dt>
    <dd>
        <a title="Libro de Inscripciones académicas" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('admon.inscripciones.book') }}">
            <i class="{{ $icon_menus['libro'] }} text-success"></i>
            Académicas
        </a>
    </dd>
    <dd>
        <a title="Libro de inscripciones administrativas" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-danger"></i>
            Administrativas
        </a>
    </dd>
</dl>