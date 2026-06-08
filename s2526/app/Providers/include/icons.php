<?php

use Illuminate\Support\Facades\View;
// INI iconos para menus fas fa-sliders-h
View::share('icon_menus', [
    //Sistema
    'config'              => 'fas fa-wrench',
    'sistema'             => 'fas fa-cog',
    'brand'               => 'fas fa-archive',

    'user'                => 'fas fa-user',
    'users'               => 'fas fa-users',
    'userplus'            => 'fas fa-user-plus',
    'profile'             => 'fas fa-id-card',
    'rol'                 => 'far fa-id-badge',

    'alert'               => 'fas fa-bell',
    'task'                => 'fas fa-tasks',
    'messege'             => 'fas fa-comments',
    'message'             => 'fas fa-comments',
    'mail'                => 'fa fa-envelope',
    'incorrect'           => 'fas fa-exclamation-triangle text-danger',
    'sendmail'            => 'fas fa-share', //<i class="fas fa-share"></i>
    'loginout'            => 'fas fa-external-link-alt',
    'logdb'               => 'fas fa-database',
    'setting'             => 'fas fa-sliders-h',
    'options'             => 'fas fa-grip-vertical',
    'clone'               => 'far fa-clone',
    'selectopt'           => 'fas fa-list',
    'tma'                 => 'fas fa-boxes',
    'inicio'              => 'fas fa-home',
    'dashboard'           => 'fas fa-tachometer-alt',
    'exclamation'         => 'fas fa-exclamation',
    'danger'              => 'fas fa-exclamation-triangle',

    // Edicion
    // 'editar'=>'fas fa-edit',
    'edit'                => 'fas fa-pen',
    'editar'              => 'fas fa-pen',
    'eliminar'            => 'fas fa-trash',
    'open'                => 'fas fa-plus',
    'nuevo'               => 'fas fa-plus-circle',
    'guardado'            => 'fas fa-save',
    'show'                => 'fas fa-info-circle',
    'btn_ctr'             => 'fas fa-bullseye',
    'crud'                => 'fas fa-align-justify',

    'blacklist'           => 'fas fa-align-justify',
    'tline'               => 'fas fa-history',
    'info'                => 'fas fa-info',
    'eye'                 => 'fas fa-eye',
    'close'               => 'fas fa-times',
    'lock'                => 'fas fa-lock',      //<i class="fas fa-lock"></i>
    'unlock'              => 'fas fa-lock-open', //<i class="fas fa-lock-open"></i>
    'minimizar'           => 'fas fa-angle-down',
    'maximizar'           => 'fas fa-angle-up',
    'opcion'              => 'fas fa-sliders-h',
    'buscar'              => 'fas fa-search',
    'registrar'           => 'fas fa-archive',
    'imprimir'            => 'fas fa-print',
    'filtro'              => 'fas fa-filter',
    'legenda'             => 'fas fa-align-left',
    'check'               => 'fas fa-check',
    'check-double'        => 'fas fa-check-double', //<i class="fas fa-check-double"></i>
    'door-closed'         => 'fas fa-door-closed',  //<i class="fas fa-door-closed"></i>
    'puntos'              => 'fas fa-ellipsis-v',
    'generate'            => 'fas fa-ellipsis-v',
    'ayuda'               => 'fa fa-info-circle',
    'ayudas'              => 'fa fa-question',
    'asistent'            => 'fas fa-forward',   //<i class="fas fa-forward"></i>
    'redo'                => 'fas fa-redo',      //<i class="fas fa-forward"></i>
    'book'                => 'fa-solid fa-book', //<i class="fa-solid fa-book"></i>

    // Gráficas
    'chartpie'            => 'fas fa-chart-pie',
    'chartbar'            => 'fas fa-chart-bar',
    'chartarea'           => 'fas fa-chart-area',
    'chartline'           => 'fas fa-chart-line',

    // SAEFL
    'estudiante'          => 'fas fa-user-edit',
    'estudiant'           => 'fas fa-user-edit',
    'estudiants'          => 'fas fa-user-edit',
    'administracion'      => 'fas fa-money-bill-alt',
    'control_estudio'     => 'fas fa-folder',
    'institucion'         => 'fas fa-building',
    'oinstitucion'        => 'fas fa-building text-info',
    'banco'               => 'fas fa-university text-primary',
    'planpagos'           => 'fas fa-th-large text-info',

    'cuentaxpagars'       => 'fas fa-th-list text-success',
    'concepto_pagos'      => 'fas fa-th text-danger',
    'isrl'                => 'fas fa-percentage', //<i class="fas fa-percentage"></i>
    'plan_beneficos'      => 'fas fa-user-check text-secondary',
    'descuentos'          => 'fas fa-percentage text-danger',

    'peducativos'         => 'fas fa-window-restore text-primary',
    'pestudios'           => 'fas fa-book-open text-info',
    'grados'              => 'fas fa-grip-vertical text-success',
    'seccions'            => 'fas fa-grip-horizontal text-danger',
    'lapsos'              => 'fas fa-stream text-info',
    'profesor'            => 'fas fa-chalkboard-teacher',
    'profesors'           => 'fas fa-chalkboard-teacher',
    'profesor_guia'       => 'fas fa-id-card-alt text-primary',
    'representante'       => 'fas fa-user-tie',

    'pago'                => 'fas fa-money-check',
    'pagos_adelantados'   => 'fas fa-money-check',
    'abonos'              => 'fas fa-money-check',
    'registropagos'       => 'fas fa-file-invoice',
    'registro_pagos'      => 'fas fa-file-invoice',
    'registrar_pago'      => 'fas fa-archive text-info',
    'cuentas_cobrar'      => 'fas fa-coins',
    'operaciones_bancos'  => 'fas fa-exchange-alt',
    'libro'               => 'fas fa-book',
    'documento'           => 'far fa-file-alt',
    'prepagos'            => 'far fa-envelope text-success',
    'exchange_rates'      => 'fas fa-chart-line text-dark',

    'notificaciones'      => 'fas fa-exclamation-circle',
    'ingresos'            => 'fas fa-coins',
    'inscripciones'       => 'fas fa-file-alt',
    'autoridades'         => 'fas fa-id-badge',

    'descuento'           => 'fas fa-percent',
    'sync_datas'          => 'fas fa-sync',
    'cronograma'          => 'fas fa-calendar-check',
    'notas'               => 'fas fa-exchange-alt',
    'list'                => 'fas fa-list',
    'pdf'                 => 'fa fa-file-pdf',
    'print'               => 'fa fa-print',
    'xls'                 => 'fas fa-file-excel',
    'csv'                 => 'fas fa-file-csv', //<i class="fas fa-file-csv"></i>
    'pensum'              => 'far fa-list-alt text-secondary',
    'pevaluacion'         => 'fas fa-th-list',
    'evaluacion'          => 'fas fa-list-ol',
    'activities'          => 'fas fa-book',
    'edescriptivas'       => 'fas fa-sliders-h',
    'aconocimiento'       => 'fas fa-book-open text-danger',
    'asignatura'          => 'fas fa-chalkboard text-danger',
    'grupo_estables'      => 'fa fa-users text-primary',
    'boletin'             => 'fab fa-wpforms',
    'carga'               => 'fas fa-arrow-alt-circle-down',
    'historico'           => 'fas fa-history text-info',
    'registro_titulos'    => 'fas fa-user-graduate text-success',
    'saldo'               => 'fas fa-wallet',
    'materia_pendientes'  => 'fas fa-grip-vertical',
    'acta_notas'          => 'fas fa-list-ol',
    'retiro'              => 'fas fa-external-link-square-alt text-danger',

    'enrollments'         => 'far fa-file-alt',

    //areas
    'SISTEMA'             => 'fas fa-cog',
    'ADMINISTRACION'      => 'fas fa-money-bill-alt',
    'CONTROL ESTUDIO'     => 'fas fa-folder',

    //collections politicals
    'coll_politicals'     => 'fas fa-th-large text-dark',
    'coll_nivels'         => 'fas fa-project-diagram text-primary',
    'coll_activities'     => 'fas fa-grip-vertical text-dark',
    'coll_debtors'        => 'fas fa-slider-h text-primary',
    'coll_messeges'       => 'fas fa-exchange-alt text-danger',
    'coll_promises'       => 'fa fa-list-alt text-primary',
    'group'               => 'fa fa-users',
    'queuing'             => 'fas fa-list-ul', //<i class="fas fa-list-ul"></i>

    'bot'                 => 'fas fa-robot',

    //Control Assistenn
    'asisst_controls'     => 'fas fa-user-clock',
    // 'asisst_control'=>'fas fa-user-clock',
    'assit_schedules'     => 'fas fa-calendar',

    //Bienestar
    'student_records'     => 'fas fa-id-card',
    'incidents'           => 'fas fa-file-contract', //<i class="fas fa-file-contract"></i>
    'estudiants'          => 'fas fa-male',
    // 'estudiants'=>'fas fa-user-edit',
    'helps'               => 'fas fa-info-circle',
    'incident_setting'    => 'fas fa-ellipsis-v',   //<i class="fas fa-ellipsis-v"></i>
    'incident_agreements' => 'far fa-handshake',    //<i class="far fa-handshake"></i>
    'fichaDigital'        => 'fas fa-file-alt',     //<i class="fas fa-file-alt"></i>
    'expedientDigital'    => 'fas fa-archive',      //<i class="fas fa-archive"></i>
    'interviews'          => 'fas fa-address-book', //<i class="fas fa-address-book"></i>
    'overviews'           => 'fas fa-user-clock',
    'census'              => 'fas fa-sticky-note',

    'calendar_events'     => 'fas fa-calendar-day', //<i class="fas fa-calendar-day"></i>

    //poll
    'poll'                => 'fas fa-boxes',
    'pollmain'            => 'fas fa-boxes',
    'questions'           => 'fas fa-question',            //<i class="fas fa-question"></i> <i class="fal fa-question-square"></i>
    'options'             => 'fas fa-prescription-bottle', //<i class="fas fa-prescription-bottle"></i>

    'payment'             => 'far fa-credit-card',     //<i class="far fa-credit-card"></i>
    'bars'                => 'fas fa-bars',            //<i class="fas fa-bars"></i>
    'description'         => 'fas fa-project-diagram', //<i class="fas fa-project-diagram"></i>

    'lessons'             => 'fas fa-book-reader', //<i class="fas fa-book-reader"></i>
    'social_actions'      => 'fas fa-user-clock',  //<i class="fas fa-user-clock"></i>

    'catchments'          => 'fas fa-align-justify',

    'inicials'            => 'fas fa-laptop',          //<i class="fas fa-warehouse"></i>
    'eiplanningwks'       => 'fas fa-columns',         //<i class="fas fa-warehouse"></i>
    'eiplanningbwks'      => 'fas fa-window-maximize', //<i class="fas fa-warehouse"></i>
    'eiprojectks'         => 'fas fa-th',              //<i class="fas fa-warehouse"></i>
    'eispecialks'         => 'fas fa-stream',          //<i class="fas fa-warehouse"></i>
    'eievaluationks'      => 'fas fa-grip-horizontal', //<i class="fas fa-warehouse"></i>
    'eifinalks'           => 'fas fa-sticky-note',     //<i class="fas fa-warehouse"></i>

    'debates'             => 'fas fa-sync',
    'plannings'           => 'far fa-file-code',      //<i class="far fa-file-code"></i>
    'competitions'        => 'fab fa-stack-exchange', //<i class="fab fa-stack-exchange"></i>
    'director'            => 'fas fa-user-shield',    //<i class="fas fa-user-shield"></i>

    'resend'              => 'fa fa-envelope',     //<i class="fa fa-envelope" aria-hidden="true"></i>
    'diagnostics'         => 'fas fa-stethoscope', //<i class="fas fa-stethoscope"></i>
    'audits'              => 'fas fa-calculator',  //<i class="fas fa-calculator"></i>

    'pases'               => 'fas fa-tasks',       //<i class="fas fa-tasks"></i>
    'bienestar'           => 'bi bi-folder2-open', //<i class="bi bi-folder2-open"></i> bootstrap 5

]);
