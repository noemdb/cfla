<a class="dropdown-item p-1" href="{{ route('administracion.dashboard') }}">
    <i class="{{ $icon_menus['chartarea'] }} text-primary"></i>
    Indicadores
</a>

<a title="Autorrespondedor" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.autoresponders.bmains.index') }}">
    <i class="{{ $icon_menus['bot'] ?? '' }}"></i>
    Autorespondedor
</a>

<a title="Actualización de Datos de los Usuarios" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.users.index') }}">
    <i class="{{ $icon_menus['user'] ?? '' }}"></i>
    Usuarios
</a>

<a title="Proceso de Matriculación" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.matriculations.catchments.index') ?? ''}}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Proceso de Matriculación
</a>


{{-- <a title="Bancos" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.banco') }}">
    <i class="{{ $icon_menus['banco'] }} text-success"></i>
    Bancos
</a>

<a title="Planes de Pago registrados" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.configuraciones.planpagos.index') }}">
    <i class="{{ $icon_menus['pago'] ?? '' }} "></i>
    Planes de Pago
</a>
<a title="Conceptos por cobrar" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.cuentaxpagars.index') }}">
    <i class="{{ $icon_menus['pago'] }} text-danger"></i>
    Conceptos por cobrar
</a>
<a title="Conceptos de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.concepto_pagos.index') }}">
    <i class="{{ $icon_menus['pago'] }} text-info"></i>
    Conceptos de Pago
</a> --}}

