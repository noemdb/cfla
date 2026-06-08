{{-- <div class="p-1 m-1">
    <i class="fas fa-file-alt fa-2x" aria-hidden="true"></i>
    Inscripciones
</div> --}}
<h5 class="text-dark pl-1">Planes de Pago</h5>

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
</a>
{{-- <a title="Planes de descuento" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.planpagos.index') }}">
    <i class="{{ $icon_menus['descuento'] }} text-warning"></i>
    Planes de descuento
</a> --}}
