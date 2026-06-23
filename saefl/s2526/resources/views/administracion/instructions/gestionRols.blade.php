{{-- resources/views/administracion/instructions/gestionRols.blade.php --}}
<div>
    
    <div class="container-fluid py-2">

        <!-- Header -->
        <div class="row mb-1">
            <div class="col-12">
                <div class="jumbotron alert-secondary py-2">
                    <h3 class="">
                        <i class="fas fa-users-cog mr-2"></i>Gestión de Usuarios
                    </h3>
                    <p class="text-muted mb-1">Manual completo de gestión de usuarios, perfiles y roles - Sistema de Modals</p>
                </div>
            </div>
        </div>

        <!-- Sistema de Pestañas -->
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="guideTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users" role="tab">
                            <i class="fas fa-users mr-1"></i>Gestión de Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="creation-tab" data-toggle="tab" href="#creation" role="tab">
                            <i class="fas fa-plus-circle mr-1"></i>Creación de Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profiles-tab" data-toggle="tab" href="#profiles" role="tab">
                            <i class="fas fa-id-card mr-1"></i>Gestión de Perfiles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="roles-tab" data-toggle="tab" href="#roles" role="tab">
                            <i class="fas fa-user-shield mr-1"></i>Gestión de Roles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="filters-tab" data-toggle="tab" href="#filters" role="tab">
                            <i class="fas fa-filter mr-1"></i>Sistema de Filtros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="faq-tab" data-toggle="tab" href="#faq-users" role="tab">
                            <i class="fas fa-question-circle mr-1"></i>Preguntas Frecuentes
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="guideTabsContent">
                    <!-- Pestaña Usuarios -->
                    <div class="tab-pane fade show active" id="users" role="tabpanel">
                        <div class="alert alert-info mb-3 py-2 my-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            En esta sección podrá gestionar todos los aspectos relacionados con las cuentas de usuario del sistema mediante <strong>modals interactivos</strong>.
                        </div>

                        <!-- Búsqueda y Filtros Rápidos -->
                        <div class="compact-card card border-left-primary">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-primary">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-primary">Búsqueda y Filtros Rápidos</h6>
                                        <p class="card-text small mb-2">
                                            El sistema cuenta con funciones de búsqueda y filtrado avanzado para encontrar usuarios específicos de manera eficiente.
                                        </p>
                                        
                                        <div class="info-box">
                                            <strong>Campos de búsqueda disponibles:</strong>
                                            <ul class="mb-1 mt-1 small">
                                                <li><strong>Nombre de usuario:</strong> Busca por el username del sistema</li>
                                                <li><strong>Nombre:</strong> Busca en el campo firstname del perfil</li>
                                                <li><strong>Apellido:</strong> Busca en el campo lastname del perfil</li>
                                            </ul>
                                        </div>

                                        <div class="mb-2">
                                            <span class="code-snippet d-inline-block mb-1">Campo de búsqueda → Escribir término → Botón <i class="fas fa-search"></i> o Enter</span>
                                        </div>

                                        <div class="warning-box">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            <strong>Consejo:</strong> Puede usar términos parciales. Por ejemplo, "mar" encontrará "María", "Mario", "Martín".
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edición de Usuarios en Modal -->
                        <div class="compact-card card border-left-warning">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-warning">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-warning">Edición de Usuario (Modal)</h6>
                                        <p class="card-text small mb-2">
                                            Modifique la información básica de la cuenta de usuario en un <strong>modal centrado</strong> que mantiene el contexto de la lista.
                                        </p>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <strong class="small">Campos editables:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Username:</strong> Nombre de usuario para login</li>
                                                    <li><strong>Email:</strong> Correo electrónico principal</li>
                                                    <li><strong>Estado:</strong> Activar/Desactivar cuenta</li>
                                                    <li><strong>Work ID:</strong> Identificador laboral (Asociación Biométrico)</li>
                                                    <li><strong>Card ID:</strong> Identificación de tarjeta</li>
                                                    <li><strong>Ident:</strong> Identificación personal</li>
                                                    <li><strong>Number ID:</strong> Número identificador</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="small">Validaciones:</strong>
                                                <ul class="small mb-1">
                                                    <li>Email debe tener formato válido y ser único</li>
                                                    <li>Username debe ser único</li>
                                                    <li>Work ID, Card ID, Ident y Number ID deben ser únicos</li>
                                                    <li>Campos obligatorios marcados con *</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <span class="code-snippet d-inline-block mb-1">Lista usuarios → Botón <i class="fas fa-pencil-alt"></i> amarillo → Modal de edición → Guardar</span>
                                        </div>

                                        <div class="warning-box">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <strong>Importante:</strong> Al cambiar el email, se actualiza automáticamente en todos los perfiles asociados (representante, profesor).
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navegación entre Módulos Modals -->
                        <div class="compact-card card border-left-info">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-info">
                                        <i class="fas fa-window-restore"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-info">Sistema de Modals</h6>
                                        <p class="card-text small mb-2">
                                            Acceda a las diferentes secciones de gestión mediante <strong>modals superpuestos</strong> que mantienen el foco en una tarea a la vez.
                                        </p>
                                        
                                        <div class="row text-center mb-2">
                                            <div class="col-md-3 mb-2">
                                                <span class="badge badge-success p-2 mb-1 d-block">Verde</span>
                                                <div class="small">
                                                    <strong>Crear Usuario</strong><br>
                                                    <small>Modal XL - Asistente completo</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <span class="badge badge-warning p-2 mb-1 d-block">Amarillo</span>
                                                <div class="small">
                                                    <strong>Editar Usuario</strong><br>
                                                    <small>Modal - Datos básicos</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <span class="badge badge-info p-2 mb-1 d-block">Azul</span>
                                                <div class="small">
                                                    <strong>Gestionar Perfil</strong><br>
                                                    <small>Modal XL - Información personal</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <span class="badge badge-success p-2 mb-1 d-block">Verde</span>
                                                <div class="small">
                                                    <strong>Administrar Roles</strong><br>
                                                    <small>Modal XL - Permisos y accesos</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="info-box">
                                            <strong>Ventajas del sistema de modals:</strong>
                                            <ul class="small mb-0">
                                                <li><strong>Enfoque concentrado:</strong> Una tarea a la vez</li>
                                                <li><strong>Contexto preservado:</strong> Lista visible en segundo plano</li>
                                                <li><strong>Navegación rápida:</strong> Cierre con botón X o clic fuera</li>
                                                <li><strong>Responsive:</strong> Se adapta a todos los dispositivos</li>
                                                <li><strong>Scroll integrado:</strong> Contenido largo manejable</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña Creación de Usuarios -->
                    <div class="tab-pane fade" id="creation" role="tabpanel">
                        <div class="alert alert-success mb-3 py-2 my-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            Cree nuevos usuarios completos con <strong>asistente integrado</strong> que incluye perfil y rol automáticamente.
                        </div>

                        <!-- Asistente de Creación Completo -->
                        <div class="compact-card card border-left-success">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-success">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-success">Asistente de Creación (Modal XL)</h6>
                                        <p class="card-text small mb-2">
                                            Cree usuarios completos en un solo proceso con <strong>tres secciones integradas</strong> y validación en tiempo real.
                                        </p>
                                        
                                        <div class="mb-2">
                                            <span class="code-snippet d-inline-block mb-1">Botón verde "Nuevo" → Modal XL con scroll → Completar 3 secciones → Crear Usuario</span>
                                        </div>

                                        <div class="info-box">
                                            <strong>Proceso automatizado:</strong>
                                            <ul class="small mb-0">
                                                <li><strong>Usuario:</strong> Cuenta de acceso al sistema</li>
                                                <li><strong>Perfil:</strong> Información personal automáticamente</li>
                                                <li><strong>Rol:</strong> Permisos y accesos configurados</li>
                                                <li><strong>Transacción segura:</strong> Todo se guarda o nada se guarda</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 1: Datos de Acceso -->
                        <div class="compact-card card border-left-primary">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-primary">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-primary">Sección 1: Datos de Acceso</h6>
                                        <p class="card-text small mb-2">
                                            Configure las credenciales de acceso al sistema con validaciones de seguridad.
                                        </p>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <strong class="small">Campos obligatorios:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Usuario:</strong> Nombre único para login (mín. 3 caracteres)</li>
                                                    <li><strong>Email:</strong> Correo válido y único en el sistema</li>
                                                    <li><strong>Contraseña:</strong> Mínimo 6 caracteres</li>
                                                    <li><strong>Confirmar Contraseña:</strong> Debe coincidir</li>
                                                    <li><strong>Estado:</strong> Activo/Inactivo desde creación</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="small">Validaciones automáticas:</strong>
                                                <ul class="small mb-1">
                                                    <li>Username único en tiempo real</li>
                                                    <li>Formato de email válido</li>
                                                    <li>Fortaleza de contraseña</li>
                                                    <li>Confirmación de contraseña</li>
                                                    <li>Estado requerido</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="warning-box">
                                            <i class="fas fa-shield-alt mr-1"></i>
                                            <strong>Seguridad:</strong> La contraseña se encripta automáticamente antes de guardarse.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 2: Datos de Identificación -->
                        <div class="compact-card card border-left-info">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-info">
                                        <i class="fas fa-address-card"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-info">Sección 2: Datos de Identificación</h6>
                                        <p class="card-text small mb-2">
                                            Complete la información de identificación laboral y personal del usuario.
                                        </p>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <strong class="small">Campos disponibles:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Número de Tarjeta:</strong> Identificación de tarjeta (única)</li>
                                                    <li><strong>Ident. Trabajador BIO:</strong> ID biométrico (numérico, único)</li>
                                                    <li><strong>Ident. Trabajador:</strong> Identificación laboral (única)</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="small">Características:</strong>
                                                <ul class="small mb-1">
                                                    <li>Todos los campos son opcionales</li>
                                                    <li>Validación de unicidad automática</li>
                                                    <li>Work ID es campo numérico</li>
                                                    <li>Integración con sistemas biométricos</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="info-box">
                                            <i class="fas fa-fingerprint mr-1"></i>
                                            <strong>Biométrico:</strong> El campo "Ident. Trabajador BIO" está optimizado para integración con sistemas de control de acceso.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 3: Datos Personales -->
                        <div class="compact-card card border-left-success">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-success">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-success">Sección 3: Datos Personales</h6>
                                        <p class="card-text small mb-2">
                                            Ingrese la información personal básica para el perfil del usuario.
                                        </p>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <strong class="small">Campos obligatorios:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Nombres:</strong> Nombre(s) de pila (mín. 2 caracteres)</li>
                                                    <li><strong>Apellidos:</strong> Apellido(s) completos (mín. 2 caracteres)</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="small">Campo opcional:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Cédula:</strong> Número de identificación personal</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="warning-box">
                                            <i class="fas fa-id-card mr-1"></i>
                                            <strong>Perfil automático:</strong> Esta información crea automáticamente el perfil asociado al usuario.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 4: Asignación de Rol -->
                        <div class="compact-card card border-left-warning">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-warning">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-warning">Sección 4: Asignación de Rol</h6>
                                        <p class="card-text small mb-2">
                                            Configure los permisos y accesos del usuario en el sistema.
                                        </p>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <strong class="small">Campos obligatorios:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Área:</strong> Departamento o sección de trabajo</li>
                                                    <li><strong>Rol:</strong> Tipo de permiso y acceso</li>
                                                    <li><strong>Fecha Inicial:</strong> Inicio de vigencia</li>
                                                    <li><strong>Fecha Final:</strong> Fin de vigencia</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="small">Campos opcionales:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Cargo:</strong> Posición específica</li>
                                                    <li><strong>Horario:</strong> Asistencia programada</li>
                                                    <li><strong>Grupo:</strong> Agrupación de usuarios</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="info-box">
                                            <strong>Validaciones de fechas:</strong>
                                            <ul class="small mb-0">
                                                <li>Fecha final debe ser igual o posterior a fecha inicial</li>
                                                <li>Formato de fecha automático</li>
                                                <li>Vigencia configurada automáticamente</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Proceso de Guardado -->
                        <div class="compact-card card border-left-danger">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-danger">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-danger">Proceso de Guardado Seguro</h6>
                                        <p class="card-text small mb-2">
                                            <strong>Transacción atómica</strong> que garantiza la integridad de los datos.
                                        </p>
                                        
                                        <div class="info-box">
                                            <strong>Secuencia de creación:</strong>
                                            <ol class="small mb-1">
                                                <li><strong>Usuario:</strong> Se crea la cuenta de acceso</li>
                                                <li><strong>Perfil:</strong> Se crea el perfil asociado al usuario</li>
                                                <li><strong>Rol:</strong> Se asigna el rol con permisos</li>
                                                <li><strong>Confirmación:</strong> Mensaje de éxito con detalles</li>
                                            </ol>
                                        </div>

                                        <div class="warning-box">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <strong>Garantía de integridad:</strong> Si falla cualquier paso, <strong>ningún dato se guarda</strong>. Evita usuarios sin perfil o sin roles.
                                        </div>

                                        <div class="mb-2">
                                            <span class="code-snippet d-inline-block mb-1">Botón "Crear Usuario" → Validación → Transacción → Mensaje éxito → Cierre automático</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña Perfiles -->
                    <div class="tab-pane fade" id="profiles" role="tabpanel">
                        <div class="alert alert-info mb-3 py-2 my-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            Gestione la información personal de los usuarios mediante <strong>modals especializados</strong>.
                        </div>

                        <!-- Acceso al Perfil en Modal -->
                        <div class="compact-card card border-left-success">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-success">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-success">Acceso al Perfil (Modal XL)</h6>
                                        <p class="card-text small mb-2">
                                            Visualice y gestione toda la información personal en un <strong>modal de pantalla completa</strong> con scroll integrado.
                                        </p>
                                        
                                        <div class="mb-2">
                                            <span class="code-snippet d-inline-block mb-1">Lista usuarios → Botón azul <i class="fas fa-user"></i> → Modal XL → Ver/Editar perfil</span>
                                        </div>

                                        <div class="info-box">
                                            <strong>Información mostrada en el perfil:</strong>
                                            <ul class="small mb-0">
                                                <li>Nombre de usuario y nombre completo</li>
                                                <li>Número de cédula (si está disponible)</li>
                                                <li>Estado de la cuenta (Activo/Inactivo)</li>
                                                <li>Formulario de edición integrado</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edición del Perfil Integrada -->
                        <div class="compact-card card border-left-success">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-success">
                                        <i class="fas fa-pencil-alt"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-success">Edición Integrada en Modal</h6>
                                        <p class="card-text small mb-2">
                                            Modifique la información personal <strong>dentro del mismo modal</strong> sin cambiar de ventana o contexto.
                                        </p>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <strong class="small">Campos editables:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Firstname (Nombre):</strong> Nombre(s) de pila</li>
                                                    <li><strong>Lastname (Apellido):</strong> Apellido(s)</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="small">Restricciones:</strong>
                                                <ul class="small mb-1">
                                                    <li>Ambos campos son obligatorios</li>
                                                    <li>Solo caracteres alfabéticos y espacios</li>
                                                    <li>Máximo 255 caracteres cada uno</li>
                                                    <li>Validación en tiempo real</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="warning-box">
                                            <i class="fas fa-sync-alt mr-1"></i>
                                            <strong>Actualización inmediata:</strong> Los cambios se reflejan instantáneamente en la lista principal sin recargar la página.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña Roles -->
                    <div class="tab-pane fade" id="roles" role="tabpanel">
                        <div class="alert alert-info mb-3 py-2 my-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            Administre los permisos y accesos mediante <strong>modals de gestión especializada</strong>.
                        </div>

                        <!-- Gestión de Roles en Modal -->
                        <div class="compact-card card border-left-warning">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-warning">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-warning">Gestión de Roles (Modal XL)</h6>
                                        <p class="card-text small mb-2">
                                            Asigne y configure roles en un <strong>modal completo</strong> que incluye lista de roles existentes y editor integrado.
                                        </p>
                                        
                                        <div class="mb-2">
                                            <span class="code-snippet d-inline-block mb-1">Lista usuarios → Botón verde <i class="fas fa-user-tag"></i> → Modal XL → Ver/Editar roles</span>
                                        </div>

                                        <div class="info-box">
                                            <strong>Vista de roles existentes:</strong>
                                            <ul class="small mb-0">
                                                <li>Lista completa de roles asignados</li>
                                                <li>Área y tipo de cada rol con colores</li>
                                                <li>Fechas de vigencia formateadas</li>
                                                <li>Horarios y grupos asignados</li>
                                                <li>Botones de edición por cada rol</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edición de Roles Integrada -->
                        <div class="compact-card card border-left-warning">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-start">
                                    <div class="step-icon bg-warning">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2 text-warning">Edición de Rol Integrada</h6>
                                        <p class="card-text small mb-2">
                                            Modifique roles específicos <strong>debajo de la lista</strong> en el mismo modal, manteniendo el contexto visual.
                                        </p>
                                        
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <strong class="small">Parámetros configurables:</strong>
                                                <ul class="small mb-1">
                                                    <li><strong>Área:</strong> Departamento o sección</li>
                                                    <li><strong>Rol:</strong> Tipo de permiso</li>
                                                    <li><strong>Cargo:</strong> Posición específica</li>
                                                    <li><strong>Grupo:</strong> Agrupación de usuarios</li>
                                                    <li><strong>Horario:</strong> Asistencia programada</li>
                                                    <li><strong>Fechas:</strong> Vigencia del rol</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="small">Campos obligatorios:</strong>
                                                <ul class="small mb-1">
                                                    <li>Área de trabajo (select)</li>
                                                    <li>Tipo de rol (select)</li>
                                                    <li>Fecha inicial (date)</li>
                                                    <li>Fecha final (date)</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="warning-box">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            <strong>Validación de fechas:</strong> El sistema verifica que la fecha final sea posterior a la fecha inicial.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña Sistema de Filtros -->
                    <div class="tab-pane fade" id="filters" role="tabpanel">
                        <!-- ... (mantener contenido existente de filtros sin cambios) ... -->
                    </div>

                    <!-- Pestaña Preguntas Frecuentes Actualizada -->
                    <div class="tab-pane fade" id="faq-users" role="tabpanel">
                        <div class="alert alert-info mb-3 py-2 my-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            Encuentre respuestas a las preguntas más comunes sobre el nuevo sistema de gestión.
                        </div>

                        <div class="accordion" id="faqAccordion">
                            <!-- FAQ 1 - Actualizada -->
                            <div class="card compact-card">
                                <div class="card-header py-2" id="faq1">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-dark" type="button" data-toggle="collapse" data-target="#faqAnswer1">
                                            <i class="fas fa-question-circle text-primary mr-2"></i>
                                            ¿Cómo funciona el nuevo sistema de modals?
                                        </button>
                                    </h6>
                                </div>
                                <div id="faqAnswer1" class="collapse show" data-parent="#faqAccordion">
                                    <div class="card-body py-2 small">
                                        <p>El sistema de modals ofrece una experiencia más enfocada:</p>
                                        <ul>
                                            <li><strong>Modal de Creación:</strong> Asistente completo en modal XL con scroll</li>
                                            <li><strong>Modal de Edición:</strong> Formulario compacto para datos básicos</li>
                                            <li><strong>Modal XL Perfil:</strong> Pantalla completa para gestión de información personal</li>
                                            <li><strong>Modal XL Roles:</strong> Vista completa con lista y editor integrado</li>
                                            <li><strong>Cierre rápido:</strong> Use la X, botón "Cerrar" o clic fuera del modal</li>
                                        </ul>
                                        <div class="warning-box">
                                            <strong>Ventaja:</strong> Puede ver la lista de usuarios en segundo plano mientras trabaja en un modal.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ Nueva - Creación de Usuarios -->
                            <div class="card compact-card">
                                <div class="card-header py-2" id="faqCreation">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-dark collapsed" type="button" data-toggle="collapse" data-target="#faqAnswerCreation">
                                            <i class="fas fa-question-circle text-success mr-2"></i>
                                            ¿Cómo creo un nuevo usuario completo con perfil y rol?
                                        </button>
                                    </h6>
                                </div>
                                <div id="faqAnswerCreation" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body py-2 small">
                                        <p>Para crear un usuario completo:</p>
                                        <ol>
                                            <li>Haga clic en el botón verde <strong>"Nuevo"</strong> en la parte superior</li>
                                            <li>Complete la <strong>Sección 1: Datos de Acceso</strong> (usuario, email, contraseña, estado)</li>
                                            <li>Complete la <strong>Sección 2: Datos de Identificación</strong> (tarjeta, biométrico, identificación)</li>
                                            <li>Complete la <strong>Sección 3: Datos Personales</strong> (nombres, apellidos, cédula)</li>
                                            <li>Complete la <strong>Sección 4: Asignación de Rol</strong> (área, rol, fechas, horario)</li>
                                            <li>Haga clic en <strong>"Crear Usuario"</strong> para guardar todo automáticamente</li>
                                        </ol>
                                        <div class="info-box">
                                            <strong>Proceso seguro:</strong> El sistema crea usuario, perfil y rol en una transacción atómica. Si algo falla, no se guarda nada.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 2 - Mantenida -->
                            <div class="card compact-card">
                                <div class="card-header py-2" id="faq2">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-dark collapsed" type="button" data-toggle="collapse" data-target="#faqAnswer2">
                                            <i class="fas fa-question-circle text-primary mr-2"></i>
                                            ¿Cómo filtro usuarios inactivos en el sistema?
                                        </button>
                                    </h6>
                                </div>
                                <div id="faqAnswer2" class="collapse" data-parent="#faqAccordion">
                                    <!-- ... (mantener contenido existente) ... -->
                                </div>
                            </div>

                            <!-- FAQ Nueva - Campos de Identificación -->
                            <div class="card compact-card">
                                <div class="card-header py-2" id="faqIdentification">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-dark collapsed" type="button" data-toggle="collapse" data-target="#faqAnswerIdentification">
                                            <i class="fas fa-question-circle text-info mr-2"></i>
                                            ¿Qué son los campos de identificación y son obligatorios?
                                        </button>
                                    </h6>
                                </div>
                                <div id="faqAnswerIdentification" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body py-2 small">
                                        <p>Los campos de identificación son opcionales pero útiles para integración:</p>
                                        <ul>
                                            <li><strong>Número de Tarjeta:</strong> Para sistemas de control de acceso con tarjetas</li>
                                            <li><strong>Ident. Trabajador BIO:</strong> Para integración con sistemas biométricos (numérico)</li>
                                            <li><strong>Ident. Trabajador:</strong> Identificación laboral alternativa</li>
                                        </ul>
                                        <div class="warning-box">
                                            <strong>Importante:</strong> Aunque son opcionales, cada campo debe ser único en el sistema si se proporciona.
                                        </div>
                                        <div class="info-box">
                                            <strong>Recomendación:</strong> Complete al menos uno de estos campos para facilitar la identificación en sistemas externos.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 3 - Mantenida -->
                            <div class="card compact-card">
                                <div class="card-header py-2" id="faq3">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-dark collapsed" type="button" data-toggle="collapse" data-target="#faqAnswer3">
                                            <i class="fas fa-question-circle text-primary mr-2"></i>
                                            ¿Por qué no veo todos los usuarios en la lista?
                                        </button>
                                    </h6>
                                </div>
                                <div id="faqAnswer3" class="collapse" data-parent="#faqAccordion">
                                    <!-- ... (mantener contenido existente) ... -->
                                </div>
                            </div>

                            <!-- FAQ 4 - Mantenida -->
                            <div class="card compact-card">
                                <div class="card-header py-2" id="faq4">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-dark collapsed" type="button" data-toggle="collapse" data-target="#faqAnswer4">
                                            <i class="fas fa-question-circle text-primary mr-2"></i>
                                            ¿Cómo edito un rol específico dentro del modal de roles?
                                        </button>
                                    </h6>
                                </div>
                                <div id="faqAnswer4" class="collapse" data-parent="#faqAccordion">
                                    <!-- ... (mantener contenido existente) ... -->
                                </div>
                            </div>

                            <!-- FAQ 5 - Mantenida -->
                            <div class="card compact-card">
                                <div class="card-header py-2" id="faq5">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-dark collapsed" type="button" data-toggle="collapse" data-target="#faqAnswer5">
                                            <i class="fas fa-question-circle text-primary mr-2"></i>
                                            ¿La paginación funciona con los filtros aplicados?
                                        </button>
                                    </h6>
                                </div>
                                <div id="faqAnswer5" class="collapse" data-parent="#faqAccordion">
                                    <!-- ... (mantener contenido existente) ... -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.gestionRols') }}', '_blank')">
            <i class="fas fa-print"></i> Versión Imprimible
        </button>
        
        <hr>

        <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
            <tr>
                <td align="center">
                    <font size="2" color="#ffffff" face="Arial">
                        Guía de Gestión de Usuarios.  - Versión 1.1
                    </font>
                </td>
            </tr>
        </table>
    </div>
    
</div>