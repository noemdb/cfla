<a title="Tasa de Cambio" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.configuraciones.exchange_rates.index') }}">
    <i class="{{ $icon_menus['exchange_rates'] ?? '' }}"></i>
    Tasa de Cambio
</a>
<a title="Eventos y días feriados" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.configuraciones.calendar_events.index') }}">
    <i class="{{ $icon_menus['calendar_events'] ?? '' }}"></i>
    Eventos y días feriados
</a>

{{-- <a title="Control de Asistencia" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.autoresponders.bmains.index') }}">
    <i class="{{ $icon_menus['asisst_controls'] ?? '' }}"></i>
    Control de Asistencia
</a> --}}

{{-- <a title="Autorrespondedor" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.autoresponders.bmains.index') }}">
    <i class="{{ $icon_menus['bot'] ?? '' }}"></i>
    Autorespondedor
</a> --}}

<a title="Bancos" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.configuraciones.banco') }}">
    <i class="{{ $icon_menus['banco'] ?? ''}}"></i>
    Bancos
</a>

<a title="Planes de Pago registrados" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.configuraciones.planpagos.index') }}">
    <i class="{{ $icon_menus['planpagos'] ?? '' }}"></i>
    Planes de Pago
</a>
<a title="Conceptos por cobrar" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.configuraciones.cuentaxpagars.index') }}">
    <i class="{{ $icon_menus['cuentaxpagars'] }}"></i>
    {{-- Conceptos por cobrar --}}
    Conceptos por Cobrar
</a>
<a title="Conceptos de Pago" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.configuraciones.concepto_pagos.index') }}">
    <i class="{{ $icon_menus['concepto_pagos'] ?? '' }}"></i>
    Cuentas por Cobrar
</a>
{{-- <a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.concepto_pagos.index') }}">
    <i class="{{ $icon_menus['plus'] ?? '' }} text-dark"></i>
    Deuda Individual
</a> --}}
<a title="Planes Benéficos" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2 disabled" href="{{ route('administracion.configuraciones.plan_beneficos.index') }}">
    <i class="{{ $icon_menus['plan_beneficos'] ?? '' }}"></i>
    Planes Benéficos
</a>
@admon
<a title="Políticas de Cobranzas" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.collections.coll_politicals.index') }}">
    <i class="{{ $icon_menus['coll_politicals'] ?? '' }}"></i>
    P. Cobranzas
</a>

{{-- <a title="Recibos de Pago rápidos" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.receibts.recibos.index') }}">
    <i class="{{ $icon_menus['registrar_pago'] ?? '' }}"></i>
    R. de pago rápidos
</a> --}}

@endadmon
{{-- <a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.configuraciones.sync_datas.index') }}">
    <i class="{{ $icon_menus['sync_datas'] ?? '' }}"></i>
    Sincronización de Datos
</a> --}}
