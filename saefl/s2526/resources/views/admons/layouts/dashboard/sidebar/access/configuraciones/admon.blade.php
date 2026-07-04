<a title="Bancos" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.banco') }}">
    <i class="{{ $icon_menus['banco'] }} text-success"></i>
    Bancos
</a>

<a title="Planes de Pago registrados" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.configuraciones.planpagos.index') }}">
    <i class="{{ $icon_menus['pago'] ?? '' }} "></i>
    Planes de Pago
</a>
<a title="Conceptos por cobrar" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.cuentaxpagars.index') }}">
    <i class="{{ $icon_menus['pago'] }} text-danger"></i>
    {{-- Conceptos por cobrar --}}
    Conceptos por Cobrar
</a>
<a title="Conceptos de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.concepto_pagos.index') }}">
    <i class="{{ $icon_menus['pago'] }} text-info"></i>
    Cuentas por Cobrar
</a>
<a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.plan_beneficos.index') }}">
    <i class="{{ $icon_menus['notificaciones'] ?? '' }} text-success"></i>
    Planes Benéficos
</a>