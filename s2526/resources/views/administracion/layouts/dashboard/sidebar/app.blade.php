<div class="sidebar-sticky" style="background-color:{{env('APP_SIDE_COLOR','#D1FED1')}} !important">
    <ul class="nav flex-column">

        {{-- INI Sistema --}}
        <li class="nav-item">
            <a class="nav-link p-1 text-dark" href="#" title="{{ Auth::user()->profile->full_name }} Rol: {{ Auth::user()->getUserRol() ?? '' }}">
                {{-- <i class="{{ $icon_menus['dashboard'] }}"></i> --}}
                {{-- Panel --}}

                {{-- <i class="{{ $icon_menus['user'] }}"></i> --}}
                <span class="d-inline-block text-truncate font-weight-bold" style="max-width: 150px;">
                <i class="{{ $icon_menus['user'] }}"></i>

                {{-- {{ Auth::user()->username }} --}}
                {{ Auth::user()->profile->full_name }}

                {{-- <br> --}}
                <span class="text-muted pt-0" style="font-size:0.6rem">
                    {{-- Rol: {{ Auth::user()->getUserRol() ?? '' }} --}}
                </span>
                </span>
                {{-- {{ Auth::user()->profile->full_name }} --}}
            </a>
            <div class="dropdown-divider mb-0"></div>
        </li>
        {{-- FIN Sistema --}}

        <li class="nav-item font-weight-bold text-dark">

            @includeWhen(Request::is('*home*'), 'administracion.layouts.dashboard.sidebar.partials.inicio')
            @includeWhen(Request::is('*configuraciones*'), 'administracion.layouts.dashboard.sidebar.partials.configuraciones')
            @includeWhen(Request::is('*estudiants*'), 'administracion.layouts.dashboard.sidebar.partials.estudiants')
            @includeWhen(Request::is('*representants*'), 'administracion.layouts.dashboard.sidebar.partials.representants')
            @includeWhen(Request::is('*polls*'), 'administracion.layouts.dashboard.sidebar.partials.polls')
            {{-- @includeWhen(Request::is('*pagos_adelantados*'), 'administracion.layouts.dashboard.sidebar.partials.pagos_adelantados') --}}
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*registropagos*')), 'administracion.layouts.dashboard.sidebar.partials.registropagos')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*prepagos*')), 'administracion.layouts.dashboard.sidebar.partials.prepagos')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*mbancarios*')), 'administracion.layouts.dashboard.sidebar.partials.mbancarios')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*cuentaxpagars*')), 'administracion.layouts.dashboard.sidebar.partials.cuentaxpagars')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*concepto_pagos*')), 'administracion.layouts.dashboard.sidebar.partials.concepto_pagos')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*administrativas*')), 'administracion.layouts.dashboard.sidebar.partials.administrativas')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*abonos*')), 'administracion.layouts.dashboard.sidebar.partials.abonos')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*creditoafavors*')), 'administracion.layouts.dashboard.sidebar.partials.creditoafavors')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*plan_beneficos*')), 'administracion.layouts.dashboard.sidebar.partials.plan_beneficos')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*libro*')), 'administracion.layouts.dashboard.sidebar.partials.libros.admon')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*payments*')), 'administracion.layouts.dashboard.sidebar.partials.payments')

            {{-- moduls --}}
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*coll_*')),'administracion.layouts.dashboard.sidebar.moduls.collections.main')
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*receibts*')),'administracion.layouts.dashboard.sidebar.moduls.receibts.main')
            @includeWhen((Auth::user()->IsControl() && Request::is('*enrollments*')),'administracion.layouts.dashboard.sidebar.partials.enrollments')
            {{-- @includeWhen((Auth::user()->IsAdmon() && Request::is('*autoresponders*')),'administracion.layouts.dashboard.sidebar.partials.autoresponders') --}}
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*asisst_controls*')),'administracion.layouts.dashboard.sidebar.partials.asisst_controls.main')

            @includeWhen((Auth::user()->IsCommon() && Request::is('*autoresponders*')),'administracion.layouts.dashboard.sidebar.partials.autoresponders')


            {{-- @includeWhen(Request::is('*cuentas_cobrar*'), 'administracion.layouts.dashboard.sidebar.partials.cuentas_cobrar') --}}
            {{-- @includeWhen(Request::is('*operaciones_bancos*'), 'administracion.layouts.dashboard.sidebar.partials.operaciones_bancos') --}}
            {{-- @includeWhen((Auth::user()->IsAdmon() && Request::is('*inscripciones*')), 'administracion.layouts.dashboard.sidebar.partials.administrativas') --}}
            @includeWhen((Auth::user()->IsControl() && Request::is('*preinscripcions*') && (!Request::is('*prepagos*'))), 'administracion.layouts.dashboard.sidebar.partials.preinscripcions')
            @includeWhen((Auth::user()->IsControl() && Request::is('*inscripciones*')), 'administracion.layouts.dashboard.sidebar.partials.academicas')
            @includeWhen((Auth::user()->IsCommon() && Request::is('*matriculations*')), 'administracion.layouts.dashboard.sidebar.partials.matriculations')
            @includeWhen((Auth::user()->IsControl() && Request::is('*pevaluacions*')), 'administracion.layouts.dashboard.sidebar.partials.pevaluacions')
            @includeWhen((Auth::user()->IsControl() && Request::is('*profesor_gestables*')), 'administracion.layouts.dashboard.sidebar.partials.profesor_gestables')
            @includeWhen((Auth::user()->IsControl() && (Request::is('*evaluacions*')) && (!Request::is('*pevaluacions*'))), 'administracion.layouts.dashboard.sidebar.partials.evaluacions')
            @includeWhen((Auth::user()->IsControl() && (Request::is('*boletins*'))), 'administracion.layouts.dashboard.sidebar.partials.boletins')
            @includeWhen((Auth::user()->IsControl() && (Request::is('*boletin_revisions*'))), 'administracion.layouts.dashboard.sidebar.partials.boletins')
            @includeWhen((Auth::user()->IsControl() && Request::is('*historico_notas*')), 'administracion.layouts.dashboard.sidebar.partials.historico_notas')
            @includeWhen((Auth::user()->IsControl() && Request::is('*registro_titulos*')), 'administracion.layouts.dashboard.sidebar.partials.registro_titulos')
            @includeWhen((Auth::user()->IsControl() && Request::is('*titulos*') && (!Request::is('*registro_titulos*'))), 'administracion.layouts.dashboard.sidebar.partials.registro_titulos')
            @includeWhen((Auth::user()->IsControl() && Request::is('*social_actions*')), 'administracion.layouts.dashboard.sidebar.partials.social_actions')
            {{-- @includeWhen(Request::is('*planpagos*'), 'administracion.layouts.dashboard.sidebar.partials.planpagos') --}}
            @includeWhen((Auth::user()->IsAdmon() && Request::is('*planpagos*')), 'administracion.layouts.dashboard.sidebar.partials.planpagos')
            {{-- @includeWhen(Request::is('*cuentaxpagars*'), 'administracion.layouts.dashboard.sidebar.partials.cuentaxpagars') --}}
            {{-- @includeWhen(Request::is('*concepto_pagos*'), 'administracion.layouts.dashboard.sidebar.partials.concepto_pagos') --}}

            @includeWhen((Auth::user()->IsControl() && Request::is('*mailers*')), 'administracion.layouts.dashboard.sidebar.partials.mailers')

            @includeWhen((Auth::user()->IsControl() && Request::is('*bienestars*')), 'administracion.layouts.dashboard.sidebar.partials.bienestars')

            {{-- @include('administracion.layouts.dashboard.sidebar.partials.inicio') --}}

        </li>

        <li class="nav-item font-weight-bold text-dark">
            @includeIf((!empty($estudiante)), 'administracion.estudiants.deck.card.estudiant_simple')
        </li>
        {{-- /home/user/code/saefl/resources/views/administracion/estudiants/deck/card/estudiant_simple.blade.php --}}

    </ul>

</div>
