
    <a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.institucion') }}">
        <i class="{{ $icon_menus['institucion'] }} text-primary"></i>
        Datos de la institución
    </a>

    <a title="Autoridades" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.autoridad') }}">
        <i class="{{ $icon_menus['autoridades'] }} text-secondary"></i>
        Autoridades
    </a>

    <a title="Programas Educativo de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.peducativo') }}">
        <i class="{{ $icon_menus['autoridades'] }} text-success"></i>
        Programas Educativo
    </a>

    <a title="Plan de Estudio de los Programas Educativos" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.pestudio') }}">
        <i class="{{ $icon_menus['autoridades'] }} text-danger"></i>
        Planes de Estudio
    </a>

    <a title="Grados de los Planes Educativos" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.grado') }}">
        <i class="{{ $icon_menus['autoridades'] }} text-warnin"></i>
        Grados
    </a>

    <a title="Autoridades" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.seccion') }}">
        <i class="{{ $icon_menus['autoridades'] }} text-info"></i>
        Secciones
    </a>

    <a title="Bancos" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.banco') }}">
        <i class="{{ $icon_menus['banco'] }} text-success"></i>
        Bancos
    </a>

    <a title="Planes de pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.planpagos.index') }}">
        <i class="{{ $icon_menus['pago'] }} text-danger"></i>
        Planes de pago
    </a>
    <a title="Conceptos por cobrar" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.cuentaxpagars.index') }}">
        <i class="{{ $icon_menus['cuentas_cobrar'] }} text-success"></i>
        Conceptos por cobrar
    </a>
    <a title="Conceptos de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.configuraciones.concepto_pagos.index') }}">
        <i class="{{ $icon_menus['cuentas_cobrar'] }} text-primary"></i>
        Conceptos de Pago
    </a>

    {{-- <a title="Planes de descuento" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['descuento'] }} text-warning"></i>
        Planes de descuento
    </a>

    <a title="Conceptos de cobro" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['pago'] }} text-info"></i>
        Conceptos de cobro
    </a>

    <a title="Cronograma de cobro" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['cronograma'] }} text-dark"></i>
        Cronograma de cobro
    </a>

    <a title="Conceptos bancarios" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['libro'] }} text-primary"></i>
        Conceptos bancarios
    </a>

    <a title="Usuarios" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['user'] }} text-secondary"></i>
        Usuarios
    </a> --}}
