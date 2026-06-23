{{-- <div class="p-1 m-1">
    <i class="fas fa-file-alt fa-2x" aria-hidden="true"></i>
    Inscripciones
</div> --}}
<h5 class="text-dark pl-1">Conceptos por cobrar</h5>

<a title="Conceptos por cobrar registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.configuraciones.cuentaxpagars.index') }}">
    <i class="{{ $icon_menus['selectopt'] ?? '' }} text-danger"></i>
    Conceptos por cobrar
</a>
<a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.cuentaxpagars.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Crear
</a>
<a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.concepto_pagos.index') }}">
    <i class="{{ $icon_menus['cuentas_cobrar'] ?? '' }} text-info"></i>
    Conceptos por Pagar
</a>

