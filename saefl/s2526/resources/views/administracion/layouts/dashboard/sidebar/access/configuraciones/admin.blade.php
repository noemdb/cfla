<a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} font-weight-bold text-primary p-1 pl-2" href="{{ route('administracion.configuraciones.institucion') }}">
    <i class="{{ $icon_menus['institucion'] ?? ''}} text-info"></i>
    Datos de la institución
</a>

<a title="Autoridades" class="{{ $class ?? 'nav-link' }} font-weight-bold text-primary p-1 pl-2" href="{{ route('administracion.configuraciones.autoridad') }}">
    <i class="{{ $icon_menus['autoridades'] ?? ''}} text-danger"></i>
    Autoridades
</a>

<a title="Listado de pagos registrados con irregularidades" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.irregulars') }}">
    <i class="{{ $icon_menus['crud'] }} text-danger"></i>
    LP con irregularidades
</a>

<a title="Listado de repuestas de las entrevistas interactivas" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.iterrogations.interviews.index') }}">
    <i class="{{ $icon_menus['crud'] }} text-success"></i>
    Entrevistas interacitivas
</a>

<a title="Listado de post para el blog" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.blogs.posts.index') }}">
    <i class="{{ $icon_menus['crud'] }} text-danger"></i>
    Post
</a>

<a title="Listado de post para el blog" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.educational.debate.index') }}">
    <i class="{{ $icon_menus['crud'] }} text-info"></i>
    Debates
</a>

<a title="Agentes Prompts" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.prompt-contexts.index') }}">
    <i class="{{ $icon_menus['crud'] }} text-info"></i>
    Agent Prompts
</a>


<a title="Diagnóstico" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.diagnostics.referents.index') }}">
    <i class="{{ $icon_menus['crud'] }} text-info"></i>
    Diagnóstico
</a>

