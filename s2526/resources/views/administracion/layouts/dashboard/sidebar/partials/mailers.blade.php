<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Evaluación">CE. Automatizados</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Correos Electrónicos Automatizados" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.mailers.index') }}">
    <i class="{{ $icon_menus['mail'] ?? '' }}  text-primary"></i>
    CE. Automatizados
</a>

<a title="Emails Enviados" class="{{ $class ?? 'nav-link text-success' }} p-1 pl-2" href="{{ route('administracion.resend.index') }}">
    <i class="{{ $icon_menus['mail'] ?? '' }}  text-success"></i>
    Emails Enviados
</a>
