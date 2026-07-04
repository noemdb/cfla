{{-- <div class="p-1 m-1">
    <i class="fas fa-file-alt fa-2x" aria-hidden="true"></i>
    Inscripciones
</div> --}}
{{-- <h5 class="text-dark pl-1">Conceptos por cobrar</h3> --}}
<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1">Conceptos por cobrar</span>
<div class="dropdown-divider mb-0"></div>

<a title="Planes de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.configuraciones.planpagos.index') }}">
    <i class="{{ $icon_menus['pago'] ?? '' }} text-info"></i>
    Planes de Pago
</a>

<a title="Conceptos por cobrar registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.configuraciones.cuentaxpagars.index') }}">
    <i class="{{ $icon_menus['pago'] ?? '' }} text-danger"></i>
    Conceptos por cobrar
</a>
<a title=" Cuentas de cobro" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.concepto_pagos.index') }}">
    <i class="{{ $icon_menus['cuentas_cobrar'] ?? '' }} text-dark"></i>
     Cuentas de cobro
</a>
<a title="Planes Benéficos" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.plan_beneficos.index') }}">
    <i class="{{ $icon_menus['notificaciones'] ?? '' }} text-success"></i>
    Planes Benéficos
</a>

