{{-- <a title="Búsqueda de Pagos Registrados" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.index') }}">
    <i class="{{ $icon_menus['buscar'] }} text-primary"></i>
    Pagos Registrados
</a> 
@admon
    <a title="Asistente para Registrar pagos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.registropagos.asistent') }}">
        <i class="{{ $icon_menus['registrar_pago'] }} text-danger"></i>
        Asistente - Registro de Pago
    </a>
@endadmon
--}}

<a title="Asistente para Registrar pagos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.individual') }}">
    <i class="{{ $icon_menus['registrar_pago'] }} text-info"></i>
    Asistente para Registrar pagos
</a>

<a title="Generar Estructura de Cobranzas [Plan de Pago, Cuentas de Cobro y Conceptos de Cobro]"
    class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.registropagos.asistent.estructura.create') }}">
    <i class="{{ $icon_menus['planpagos'] ?? null }} text-success"></i>
    Generar Estructura
</a>

<a title="Asistente para la gestión de Dedua Individual" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.registropagos.asistent.individual') }}">
    <i class="{{ $icon_menus['estudiante'] ?? null }} text-info"></i>
    <span class="text-dark">Asistente D. Individual</span>
</a>

<a title="Listado de Pagos Registrados" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.crud') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Listado Pagos Reg.
</a>


<a title="Registrar Abonos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.abonos.index') }}">
    <i class="{{ $icon_menus['pagos_adelantados'] }} text-success"></i>
    Registrar Abonos
</a>

<a title="Listado de Creditos a favor" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.creditoafavors.crud') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Listado CAF
</a>

<a title="Listado de Movimientos Bancarios" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.ingresos.crud') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Listado Mov. Bancarios
</a>

@admon
    <a title="Listado de pagos adelantados" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.registropagos.adelantados') }}">
        <i class="{{ $icon_menus['crud'] }} text-info"></i>
        Pagos adelantados
    </a>
@endadmon
<a title="Listado de Representantes con conceptos de cobro pendientes" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.cuentaxpagars') }}">
    <i class="{{ $icon_menus['cuentaxpagars'] }}"></i>
    Conceptos pendientes
</a>
{{-- <a title="Listado de Representantes con conceptos de cobro pendientes" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.conceptopagos') }}">
    <i class="{{ $icon_menus['concepto_pagos'] }}"></i>
    Cuentas pendientes
</a> --}}


<a title="Listado de estudiantes con conceptos de cobro pendientes" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.cuentaxpagars.estudiants') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Conceptos pendientes p.Estudiante
</a>


<a title="Listado de Representantes con conceptos de cobro individuales pendientes"
    class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.cuentaxpagars.individual') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Conceptos pendientes Individual
</a>



{{-- <a title="Movimientos Bancarios XLS" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.mbancarios.crud') }}">
    <i class="{{ $icon_menus['banco'] ?? '' }} text-success"></i>
    Mov. Bancarios CSV
</a>

<a title="Verificación y Registro de Prepagos (Notificaciones de Pago) - XLS" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.prepagos.crud') }}">
    <i class="{{ $icon_menus['prepagos'] ?? '' }}"></i>
    Notificaciones CSV
</a> --}}

<a title="Listado de los reportes de pago registrado por los representantes"
    class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.payments.crud') }}">
    <i class="{{ $icon_menus['prepagos'] ?? '' }}"></i>
    Reportes de Pago
</a>

<a title="Listado de los reportes de pago registrado por los representantes"
    class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.transactions.index') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
    Rep. Botón de pago
</a>
{{-- <a title="Listado de los reportes de pago registrado por los representantes para Inscripción" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.payments.inscriptions') }}">
    <i class="{{ $icon_menus['prepagos'] ?? '' }} text-dark"></i>
    Reportes de P. Inscripción
</a> --}}


<a title="Recibos de Pago rápidos" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2"
    href="{{ route('administracion.receibts.recibos.index') }}">
    <i class="{{ $icon_menus['registrar_pago'] ?? '' }}"></i>
    R. de pago rápidos
</a>

<a title="Registro de devoluciones" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2"
    href="{{ route('administracion.refunds.index') }}">
    <i class="{{ $icon_menus['registrar_pago'] ?? '' }}"></i>
    Reg. de devoluciones
</a>


<a title="Conceptos de cobros Incobrables" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.cuentaxpagars.account_bad') }}">
    <i class="{{ $icon_menus['cuentas_cobrar'] ?? '' }} text-danger"></i>
    Cta. Incobrables
</a>



<a title="Morosidad por Planes de Estudio" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.cuentaxpagars.pestudios.late_payment') }}">
    <i class="{{ $icon_menus['coll_promises'] ?? '' }} text-danger"></i>
    Morosidad por Planes de Estudio
</a>

<a title="Anulaciones de Pagos" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.registropagos.cancelations') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-danger"></i>
    Anulaciones de Pagos
</a>

<a title="Generar Recargos" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.registropagos.recargo.morosidad') }}">
    <i class="fa fa-money-bill-alt"></i> Generar Recargos
</a>
