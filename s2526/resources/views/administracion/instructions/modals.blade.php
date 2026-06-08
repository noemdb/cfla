{{-- Modal con Menú Vertical Colapsible --}}
<div class="modal fade" id="navigationModal" tabindex="-1" role="dialog" aria-labelledby="navigationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="navigationModalLabel">
                    <i class="fas fa-bars mr-2"></i>Guías Instruccionales
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex">
                    {{-- Sidebar --}}
                    <div class="bg-light border-right collapsed" id="sidebarContainer"
                        style="width: 48px; transition: width 0.3s ease; position: relative;">
                        {{-- Toggle Button --}}
                        <button type="button" class="btn btn-sm btn-light rounded-circle" id="sidebarToggle"
                            style="position: absolute; top: 10px; right: -12px; z-index: 10; width: 24px; height: 24px; padding: 0; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
                        </button>

                        <div style="overflow-x: hidden; padding-top: 2.5rem;">
                            <nav class="nav flex-column">

                                {{-- Dashboard - Estructura Cobranzas --}}
                                <a class="nav-link active d-flex align-items-center menu-item" href="#dashboard"
                                    data-partial="estruturaCobranzas"
                                    data-route="{{ route('helpers.estruturaCobranzas') }}"
                                    style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid #0056b3; background-color: #007bff; color: white !important; white-space: nowrap;">
                                    <i class="fas fa-money-check" style="min-width: 20px; text-align: center;"></i>
                                    <span class="ml-2 nav-text">Estructura de Cobranza</span>
                                </a>

                                @admon
                                    <a class="nav-link d-flex align-items-center menu-item" href="#asistenteRegistroPago"
                                        data-partial="asistenteRegistroPago"
                                        data-route="{{ route('helpers.asistenteRegistroPago') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-money-check" style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Asistente Registro de Pago</span>
                                    </a>

                                    <a class="nav-link d-flex align-items-center menu-item" href="#asistenteIndividual"
                                        data-partial="asistenteIndividual"
                                        data-route="{{ route('helpers.asistenteIndividual') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-user-plus text-primary"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Asistente Deuda Individual</span>
                                    </a>

                                    <a class="nav-link d-flex align-items-center menu-item" href="#cuentaxpagarsList"
                                        data-partial="cuentaxpagarsList"
                                        data-route="{{ route('helpers.cuentaxpagarsList') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-th-list text-success"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Listado Conceptos de cobro pendiente.</span>
                                    </a>

                                    {{-- Gestión de Roles --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#gestion-roles"
                                        data-partial="gestionRols" data-route="{{ route('helpers.gestionRols') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-user-tag" style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Gestión de Roles</span>
                                    </a>

                                    {{-- Conceptos de Cobro --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#cuentas-pagar"
                                        data-partial="cuentasxpagars" data-route="{{ route('helpers.cuentasxpagars') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-file-invoice-dollar"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Conceptos de Cobro</span>
                                    </a>

                                    {{-- Cuentas de Cobro --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#conceptos-pagar"
                                        data-partial="conceptopagos" data-route="{{ route('helpers.conceptopagos') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-coins text-dark" style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Cuentas de Cobro</span>
                                    </a>

                                    {{-- Morosidad --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#morosidad"
                                        data-partial="morosidad" data-route="{{ route('helpers.morosidad') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-exclamation-triangle"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Morosidad</span>
                                    </a>

                                    {{-- Cancelaciones --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#cancelaciones"
                                        data-partial="cancelations" data-route="{{ route('helpers.cancelations') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-ban" style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Cancelaciones</span>
                                    </a>

                                    {{-- Restablecimiento BIO --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#restablecimiento-bio"
                                        data-partial="restablecimientoBIO"
                                        data-route="{{ route('helpers.restablecimientoBIO') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-fingerprint" style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Restablecimiento BIO</span>
                                    </a>

                                    {{-- Liberaciones --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#liberaciones"
                                        data-partial="liberaciones" data-route="{{ route('helpers.liberaciones') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-hand-holding-usd"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Liberaciones</span>
                                    </a>

                                    {{-- Retiros --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#retiros"
                                        data-partial="retiros" data-route="{{ route('helpers.retiros') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-user-minus text-danger"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Retiros</span>
                                    </a>

                                    {{-- Listado de saldos --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#representantsSaldos"
                                        data-partial="representantsSaldos"
                                        data-route="{{ route('helpers.representants.saldos') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-align-justify text-danger"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Listado de Saldos</span>
                                    </a>

                                    {{-- Listado de Deudores por Cuota --}}
                                    <a class="nav-link d-flex align-items-center menu-item"
                                        href="#representantsSaldosDate" data-partial="representantsSaldosDate"
                                        data-route="{{ route('helpers.representants.saldosDate') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-file-invoice-dollar text-danger"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Listado de Deudores por Cuota</span>
                                    </a>

                                    {{-- Libros de Facturación --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#bancosLibros"
                                        data-partial="bancosLibros" data-route="{{ route('helpers.bancos.libros') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-book text-dark" style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Libros de Facturación</span>
                                    </a>

                                    {{-- Histórico de pagos --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#representantsHistorico"
                                        data-partial="representantsHistorico"
                                        data-route="{{ route('helpers.representants.historico') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-user-tie text-dark"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Histórico de Pagos</span>
                                    </a>

                                    {{-- Retiros con pronto pago --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#retirosProntoPago"
                                        data-partial="retirosProntoPago"
                                        data-route="{{ route('helpers.retiros.pronto.pago') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-share text-dark"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Retiros con pronto pago</span>
                                    </a>

                                    {{-- Listado de pagos registrados --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#retirosProntoPago"
                                        data-partial="retirosProntoPago"
                                        data-route="{{ route('helpers.registropagos.listado') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-align-justify text-success"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Listado de pagos registrados</span>
                                    </a>

                                    {{-- Listado de movimientos bancarios --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#ingresosListado"
                                        data-partial="ingresosListado"
                                        data-route="{{ route('helpers.ingresos.listado') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-align-justify text-danger"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Listado de Ingresos Registrados</span>
                                    </a>

                                    {{-- Listado de movimientos bancarios --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#paymentsListado"
                                        data-partial="paymentsListado"
                                        data-route="{{ route('helpers.payments.listado') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-align-justify text-info"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Listado de reportes de pago</span>
                                    </a>

                                    {{-- Asistente Inscripciones administrativas --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#wizardAdministrativas"
                                        data-partial="wizardAdministrativas"
                                        data-route="{{ route('helpers.wizard.administrativas') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-file-alt text-info"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Asistente Inscripciones administrativas</span>
                                    </a>

                                    {{-- Envío de notificaciones de cobranza --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#wizardAdministrativas"
                                        data-partial="wizardAdministrativas"
                                        data-route="{{ route('helpers.send.notifications.collection') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-th-large text-dark"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Envío de notificaciones de cobranza</span>
                                    </a>

                                    {{-- Envío de notificaciones de cobranza --}}
                                    <a class="nav-link d-flex align-items-center menu-item"
                                        href="#representantsBlacklistManage" data-partial="representantsBlacklistManage"
                                        data-route="{{ route('helpers.representants.blacklist.manage') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-exclamation-triangle text-danger"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Representantes con Morosidad y Restricciones
                                            [LN]</span>
                                    </a>

                                    <a class="nav-link d-flex align-items-center menu-item" href="#CalendarEventManage"
                                        data-partial="CalendarEventManage"
                                        data-route="{{ route('helpers.CalendarEvent.Manage') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-clock text-dark"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Gestionar calendario Eventos y Lunes Bancario</span>
                                    </a>

                                    <a class="nav-link d-flex align-items-center menu-item" href="#indicatorsAdmon"
                                        data-partial="indicatorsAdmon"
                                        data-route="{{ route('helpers.indicatorsAdmon') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-chart-line text-dark"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Guía de Indicadores Administrativos</span>
                                    </a>
                                @endadmon

                                @control
                                    <a class="nav-link d-flex align-items-center menu-item" href="#boletinRevision"
                                        data-partial="boletinRevision"
                                        data-route="{{ route('helpers.boletinRevision') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-clipboard-check text-warning"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Gestión de Notas de Revisión</span>
                                    </a>

                                    <a class="nav-link d-flex align-items-center menu-item" href="#indicatorsControl"
                                        data-partial="indicatorsControl"
                                        data-route="{{ route('helpers.indicatorsControl') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-chart-pie text-dark"
                                            style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Guía de Indicadores Académicos</span>
                                    </a>

                                    {{-- Gestión de Pases --}}
                                    <a class="nav-link d-flex align-items-center menu-item" href="#gestion-pases"
                                        data-partial="pases" data-route="{{ route('helpers.evaluacion.pases') }}"
                                        style="color: #333; padding: 0.75rem 1rem; border-left: 3px solid transparent; white-space: nowrap;">
                                        <i class="fas fa-user-tag" style="min-width: 20px; text-align: center;"></i>
                                        <span class="ml-2 nav-text">Gestión de Pases</span>
                                    </a>
                                @endcontrol

                            </nav>
                        </div>
                    </div>

                    {{-- Content Area --}}
                    <div class="flex-fill" id="contentArea">
                        <div class="p-1" id="dynamicContent">
                            {{-- Contenido inicial --}}
                            @include('administracion.instructions.estruturaCobranzas')
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>

    {{-- Botón Scroll Top Flotante --}}
    <button type="button" class="btn btn-primary rounded-circle shadow scroll-top-btn" id="modalScrollTop"
        style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 1060; width: 50px; height: 50px; display: none; transition: all 0.3s ease;">
        <i class="fas fa-chevron-up"></i>
    </button>

    @section('scripts')
        @parent
        <script>
            $(document).ready(function() {
                console.log('[v3] Modal navigation with scroll top initialized');

                // CSRF token para las peticiones AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Variables
                const $scrollTopBtn = $('#modalScrollTop');
                const $modal = $('#navigationModal');
                const $contentArea = $('#contentArea');

                // Toggle sidebar
                $('#sidebarToggle').on('click', function() {
                    var $sidebar = $('#sidebarContainer');
                    $sidebar.toggleClass('collapsed');

                    if ($sidebar.hasClass('collapsed')) {
                        $sidebar.css('width', '48px');
                        $sidebar.find('.collapse').collapse('hide');
                        console.log('[v3] Sidebar collapsed to 48px');
                    } else {
                        $sidebar.css('width', '250px');
                        console.log('[v3] Sidebar expanded to 250px');
                    }
                });

                // Manejar clicks en los items del menú
                $('#sidebarContainer .menu-item').on('click', function(e) {
                    e.preventDefault();

                    const $this = $(this);
                    const partial = $this.data('partial');
                    const route = $this.data('route');
                    const section = $this.attr('href').replace('#', '');

                    if (!route) {
                        console.error('[v3] No route defined for:', partial);
                        return;
                    }

                    // Remover active de todos los links
                    $('#sidebarContainer .nav-link').removeClass('active').css({
                        'background-color': '',
                        'color': '#333',
                        'border-left-color': 'transparent'
                    });

                    // Agregar active al link clickeado
                    $this.addClass('active').css({
                        'background-color': '#007bff',
                        'color': 'white',
                        'border-left-color': '#0056b3'
                    });

                    // Cerrar sidebar si está en móvil
                    if ($(window).width() < 768 && !$('#sidebarContainer').hasClass('collapsed')) {
                        $('#sidebarContainer').addClass('collapsed').css('width', '48px');
                    }

                    // Cargar el partial via AJAX
                    loadPartial(route, section);
                });

                // Función para cargar partials via AJAX
                function loadPartial(route, section = 'dashboard') {
                    console.log('[v3] Loading partial from route:', route);

                    // Mostrar loading
                    $('#dynamicContent').html(`
                    <div class="content-loading">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando contenido...</p>
                        </div>
                    </div>
                `);

                    // Ocultar botón scroll top durante la carga
                    $scrollTopBtn.hide();

                    // Hacer petición AJAX
                    $.ajax({
                        url: route,
                        type: 'GET',
                        dataType: 'html',
                        success: function(data) {
                            $('#dynamicContent').html(data);
                            console.log('[v3] Partial loaded successfully:', route);

                            // Scroll to top después de cargar nuevo contenido
                            $contentArea.scrollTop(0);

                            // Inicializar scroll events para el nuevo contenido
                            initScrollEvents();

                            // Ejecutar scripts dentro del partial si los hay
                            $('#dynamicContent script').each(function() {
                                eval($(this).text());
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('[v3] Error loading partial:', error);
                            $('#dynamicContent').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Error al cargar el contenido: ${error}
                            </div>
                        `);

                            // Inicializar scroll events incluso en error
                            initScrollEvents();
                        }
                    });
                }

                // Función para inicializar eventos de scroll
                function initScrollEvents() {
                    // Remover eventos previos
                    $contentArea.off('scroll');

                    // Mostrar/ocultar botón scroll top basado en scroll position
                    $contentArea.on('scroll', function() {
                        if ($(this).scrollTop() > 300) {
                            $scrollTopBtn.addClass('show').fadeIn(300);
                        } else {
                            $scrollTopBtn.removeClass('show').fadeOut(300);
                        }
                    });
                }

                // Scroll to top function
                $scrollTopBtn.on('click', function() {
                    $contentArea.animate({
                        scrollTop: 0
                    }, 500);

                    // Ocultar botón después del scroll
                    $(this).removeClass('show').fadeOut(300);
                });

                // Manejar eventos del modal
                $modal.on('show.bs.modal', function() {
                    console.log('[v3] Modal opened');
                    // Inicializar scroll events cuando se abre el modal
                    setTimeout(initScrollEvents, 100);
                });

                $modal.on('hidden.bs.modal', function() {
                    console.log('[v3] Modal closed');
                    // Ocultar botón scroll top cuando se cierra el modal
                    $scrollTopBtn.removeClass('show').hide();
                    // Remover eventos de scroll
                    $contentArea.off('scroll');
                });

                // Cerrar otros submenús cuando se abre uno nuevo
                $('#sidebarContainer .nav-link[data-toggle="collapse"]').on('click', function(e) {
                    if ($('#sidebarContainer').hasClass('collapsed')) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    var target = $(this).attr('href');
                    $('#sidebarContainer .collapse').not(target).collapse('hide');
                });

                // Inicializar tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Manejar resize de ventana
                $(window).on('resize', function() {
                    if ($(window).width() < 768) {
                        $('#sidebarContainer').addClass('collapsed').css('width', '48px');
                    }

                    // Ajustar posición del botón scroll top basado en el estado del sidebar
                    adjustScrollTopPosition();
                });

                // Función para ajustar posición del botón scroll top
                function adjustScrollTopPosition() {
                    const $sidebar = $('#sidebarContainer');
                    if ($sidebar.hasClass('collapsed')) {
                        $scrollTopBtn.css('left', '50%');
                    } else {
                        // Ajustar para compensar el sidebar expandido
                        const sidebarWidth = $sidebar.outerWidth();
                        $scrollTopBtn.css('left', `calc(50% + ${sidebarWidth / 2}px)`);
                    }
                }

                // Ajustar posición cuando se toggle el sidebar
                $('#sidebarToggle').on('click', function() {
                    setTimeout(adjustScrollTopPosition, 300); // Esperar a la animación
                });

                // Inicializar posición
                adjustScrollTopPosition();
            });
        </script>
    @endsection


    @section('stylesheet')
        @parent
        <style>
            /* Estilos existentes... */

            /* Botón Scroll Top */
            .scroll-top-btn {
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 1060;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background-color: #007bff;
                border: none;
                color: white;
                font-size: 1.2rem;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                transition: all 0.3s ease;
                display: none;
            }

            .scroll-top-btn:hover {
                background-color: #0056b3;
                transform: translateX(-50%) translateY(-2px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
            }

            .scroll-top-btn:active {
                transform: translateX(-50%) translateY(0);
            }

            .scroll-top-btn.show {
                display: block;
                animation: fadeInUp 0.3s ease;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateX(-50%) translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
            }

            /* Ajustar para cuando el sidebar está expandido */
            #sidebarContainer:not(.collapsed)~#contentArea .scroll-top-btn {
                left: calc(50% + 125px);
                /* Ajusta según el ancho del sidebar */
            }

            /* Responsive */
            @media (max-width: 768px) {
                .scroll-top-btn {
                    bottom: 15px;
                    width: 45px;
                    height: 45px;
                    font-size: 1.1rem;
                }
            }
        </style>
    @endsection


</div>

{{-- Botón Flotante para abrir el modal --}}
<button type="button" class="btn btn-primary rounded-circle shadow floating-btn" data-toggle="modal"
    data-target="#navigationModal">
    <i class="fas fa-bars fa-lg"></i>
</button>
