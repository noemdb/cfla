{{-- @if (Auth::user()->IsAdmon()) --}}
    <div class="dropdown-divider mb-0"></div>
    <a title="Gestión de Procesos de Consultas" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.polls.index') }}">
        <i class="{{ $icon_menus['setting'] ?? '' }} text-dark"></i>
        <span class="text-dark">Gestión de Encuestas</span>
    </a>
    <a title="Preguntas" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.polls.questions.index') }}">
        <i class="{{ $icon_menus['questions'] ?? '' }} text-info"></i>
        <span class="text-dark">Preguntas</span>
    </a>

    <a title="Preguntas" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.polls.options.index') }}">
        <i class="{{ $icon_menus['options'] ?? '' }} text-dark"></i>
        <span class="text-dark">Opciones</span>
    </a>

    <a title="Participantes" class="{{ $class ?? 'nav-link text-success' }} p-1 pl-2" href="{{ route('administracion.polls.competitors') }}">
        <i class="{{ $icon_menus['user'] ?? '' }}"></i>
        Participantes
    </a>

    <a title="Informes de Participación" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.polls.analyzers') }}">
        <i class="{{ $icon_menus['chartarea'] ?? '' }}"></i>
        Informes de Participación
    </a>
{{-- @endif --}}
