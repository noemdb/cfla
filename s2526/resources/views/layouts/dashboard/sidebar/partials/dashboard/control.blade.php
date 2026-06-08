<dl class="mb-1 pl-1">
    <dt>Inscricpciones</dt>
    <dd>
        <a title="Libro de Inscripciones académicas" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.inscripciones.index') }}">
            <i class="{{ $icon_menus['libro'] }} text-success"></i>
            Académicas
        </a>
    </dd>
</dl>