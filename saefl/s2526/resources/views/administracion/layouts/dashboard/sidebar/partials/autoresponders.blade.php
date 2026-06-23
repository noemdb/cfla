<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1">Autorespondedores</span>
<div class="dropdown-divider mb-0"></div>

<a title="Planes de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.autoresponders.bmains.index') }}">
    <i class="{{ $icon_menus['bot'] ?? '' }} text-info"></i>
    Principales
</a>
<a title="Planes de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.autoresponders.boptions.index') }}">
    <i class="{{ $icon_menus['bot'] ?? '' }} text-success"></i>
    Opciones
</a>
<a title="Planes de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.autoresponders.bmesseges.index') }}">
    <i class="{{ $icon_menus['bot'] ?? '' }} text-danger"></i>
    Mensajes
</a>
</a>
<a title="Listado de interacciones registradas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.autoresponders.metas.chat') }}">
    <i class="{{ $icon_menus['bot'] ?? '' }} text-primary"></i>
    Meta Chats
</a>

<a title="Mensajes WAB META" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.autoresponders.metas.ws') }}">
    <i class="{{ $icon_menus['bot'] ?? '' }} text-dark"></i>
    Mensajes WAB META
</a>
