<div class="tab-pane fade" id="authentication" role="tabpanel">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-lock mr-2"></i>Caso de Uso: Autenticación de Usuarios</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5>Descripción</h5>
                    <p class="text-muted">
                        Este caso de uso describe el proceso de autenticación segura que permite a los docentes
                        acceder al sistema SAEFL - Educación Inicial con sus credenciales válidas. El sistema
                        implementa múltiples capas de seguridad, incluyendo validación de credenciales,
                        verificación de permisos específicos para educación inicial, gestión de sesiones
                        y control de acceso basado en roles.
                    </p>

                    <div class="mt-3">
                        <h6><i class="fas fa-list-ul mr-2"></i>Funcionalidades Principales:</h6>
                        <ul class="text-muted">
                            <li>Inicio de sesión seguro con validación de credenciales</li>
                            <li>Verificación de permisos específicos para educación inicial</li>
                            <li>Gestión automática de sesiones con timeout configurable</li>
                            <li>Registro de actividad y auditoría de accesos</li>
                            <li>Recuperación de contraseñas con validación por email</li>
                            <li>Cambio de contraseñas con políticas de seguridad</li>
                            <li>Cierre de sesión seguro con limpieza de datos</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-users mr-2"></i>Actores del Sistema</h6>
                            <ul class="mb-0 small">
                                <li><strong>Docente:</strong> Usuario principal</li>
                                <li><strong>Sistema:</strong> Validador de credenciales</li>
                                <li><strong>Middleware:</strong> Verificador de permisos</li>
                                <li><strong>Base de Datos:</strong> Almacén de usuarios</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-database mr-2"></i>Modelos Involucrados</h6>
                            <ul class="mb-0 small">
                                <li>User</li>
                                <li>Autoridad</li>
                                <li>Profesor</li>
                                <li>Session (Laravel)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-shield-alt mr-2"></i>Middleware de Seguridad</h6>
                            <ul class="mb-0 small">
                                <li><code>auth</code> - Autenticación básica</li>
                                <li><code>is_inicial</code> - Permisos específicos</li>
                                <li><code>throttle</code> - Limitación de intentos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Diagrama Principal Extendido -->
            <div class="diagram-container">
                <h5 class="text-center mb-3"><i class="fas fa-sitemap mr-2"></i>Diagrama Completo de Casos de Uso</h5>
                <div class="mermaid">
                    graph TD
                        A["👤 Docente"] --> B["Acceder al Sistema"]
                        A --> C["Recuperar Contraseña"]
                        A --> D["Cambiar Contraseña"]
                        A --> E["Cerrar Sesión"]
                        A --> F["Ver Perfil"]

                        B --> G["Ingresar Credenciales"]
                        B --> H["Validar Datos"]
                        B --> I["Verificar Permisos"]
                        B --> J["Crear Sesión"]
                        B --> K["Redirigir a Dashboard"]

                        C --> L["Solicitar Recuperación"]
                        C --> M["Validar Email"]
                        C --> N["Enviar Token"]
                        C --> O["Restablecer Contraseña"]

                        D --> P["Verificar Contraseña Actual"]
                        D --> Q["Validar Nueva Contraseña"]
                        D --> R["Aplicar Políticas"]
                        D --> S["Actualizar Credenciales"]

                        E --> T["Invalidar Sesión"]
                        E --> U["Limpiar Cookies"]
                        E --> V["Registrar Logout"]
                        E --> W["Redirigir a Login"]

                        F --> X["Mostrar Información Personal"]
                        F --> Y["Mostrar Permisos"]
                        F --> Z["Mostrar Historial de Acceso"]

                        AA["🔒 Sistema de Autenticación"] --> BB["Validar Credenciales"]
                        AA --> CC["Gestionar Sesiones"]
                        AA --> DD["Controlar Acceso"]
                        AA --> EE["Auditar Actividad"]

                        FF["🛡️ Middleware is_inicial"] --> GG["Verificar Área"]
                        FF --> HH["Validar Rol"]
                        FF --> II["Comprobar Autoridad"]

                        style A fill:#e1f5fe
                        style AA fill:#fff3e0
                        style FF fill:#f3e5f5
                        style B fill:#e8f5e8
                        style C fill:#fff8e1
                        style D fill:#ffebee
                </div>
            </div>

            <!-- Flujo de Autenticación Detallado -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-route mr-2"></i>Flujo Detallado de Autenticación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-sign-in-alt fa-2x text-primary mb-2"></i>
                                            <h6>1. Acceso</h6>
                                            <p class="small text-muted mb-0">
                                                Usuario accede a la URL del sistema
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-keyboard fa-2x text-info mb-2"></i>
                                            <h6>2. Credenciales</h6>
                                            <p class="small text-muted mb-0">
                                                Ingreso de usuario y contraseña
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                            <h6>3. Validación</h6>
                                            <p class="small text-muted mb-0">
                                                Verificación en base de datos
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-user-shield fa-2x text-warning mb-2"></i>
                                            <h6>4. Permisos</h6>
                                            <p class="small text-muted mb-0">
                                                Verificación de rol y área
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-key fa-2x text-danger mb-2"></i>
                                            <h6>5. Sesión</h6>
                                            <p class="small text-muted mb-0">
                                                Creación de sesión segura
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-tachometer-alt fa-2x text-secondary mb-2"></i>
                                            <h6>6. Dashboard</h6>
                                            <p class="small text-muted mb-0">
                                                Redirección al panel principal
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middleware is_inicial Detallado -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-cogs mr-2"></i>Middleware: is_inicial</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Middleware personalizado que verifica si el usuario autenticado
                                tiene permisos específicos para acceder al módulo de educación inicial.
                            </p>

                            <h6 class="text-warning">Verificaciones Realizadas:</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Área del Usuario:</strong> Debe ser "Educación Inicial"</li>
                                <li><strong>Rol Activo:</strong> Docente con permisos vigentes</li>
                                <li><strong>Autoridad Asociada:</strong> Registro en tabla de autoridades</li>
                                <li><strong>Estado del Usuario:</strong> Cuenta activa y no suspendida</li>
                            </ul>

                            <h6 class="text-danger">Acciones en Caso de Fallo:</h6>
                            <ul class="small text-muted mb-0">
                                <li>Redirección a página de acceso denegado</li>
                                <li>Registro del intento de acceso no autorizado</li>
                                <li>Mensaje de error específico al usuario</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-code mr-2"></i>Implementación del Middleware</h6>
                        </div>
                        <div class="card-body">
                            <pre class="small text-muted mb-3"><code>// app/Http/Middleware/IsInicial.php
public function handle($request, Closure $next)
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    // Verificar área
    if ($user->area !== 'Educación Inicial') {
        abort(403, 'Acceso denegado');
    }

    // Verificar autoridad
    $autoridad = Autoridad::where('user_id', $user->id)
                          ->first();

    if (!$autoridad) {
        abort(403, 'Sin autorización');
    }

    return $next($request);
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gestión de Sesiones -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-clock mr-2"></i>Gestión de Sesiones</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-success">Configuración de Sesión</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Duración:</strong> 120 minutos de inactividad</li>
                                <li><strong>Driver:</strong> Base de datos (sessions table)</li>
                                <li><strong>Encriptación:</strong> AES-256-CBC</li>
                                <li><strong>HttpOnly:</strong> Cookies seguras</li>
                                <li><strong>SameSite:</strong> Protección CSRF</li>
                            </ul>

                            <h6 class="text-info">Datos Almacenados en Sesión</h6>
                            <ul class="small text-muted mb-3">
                                <li>ID del usuario autenticado</li>
                                <li>Información de autoridad</li>
                                <li>Permisos y roles activos</li>
                                <li>Timestamp de último acceso</li>
                                <li>Configuraciones personalizadas</li>
                            </ul>

                            <h6 class="text-warning">Limpieza Automática</h6>
                            <ul class="small text-muted mb-0">
                                <li>Sesiones expiradas eliminadas cada hora</li>
                                <li>Logout automático por inactividad</li>
                                <li>Limpieza de datos temporales</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-shield-alt mr-2"></i>Medidas de Seguridad</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-danger">Protección contra Ataques</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Brute Force:</strong> Limitación de intentos (5 por minuto)</li>
                                <li><strong>Session Hijacking:</strong> Regeneración de ID de sesión</li>
                                <li><strong>CSRF:</strong> Tokens de verificación</li>
                                <li><strong>XSS:</strong> Sanitización de datos</li>
                                <li><strong>SQL Injection:</strong> Consultas preparadas</li>
                            </ul>

                            <h6 class="text-warning">Políticas de Contraseña</h6>
                            <ul class="small text-muted mb-3">
                                <li>Mínimo 8 caracteres</li>
                                <li>Al menos una mayúscula</li>
                                <li>Al menos un número</li>
                                <li>Caracteres especiales recomendados</li>
                                <li>No reutilizar últimas 3 contraseñas</li>
                            </ul>

                            <h6 class="text-info">Auditoría y Logs</h6>
                            <ul class="small text-muted mb-0">
                                <li>Registro de todos los accesos</li>
                                <li>Log de intentos fallidos</li>
                                <li>Seguimiento de cambios de contraseña</li>
                                <li>Alertas de actividad sospechosa</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recuperación de Contraseñas -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-key mr-2"></i>Proceso de Recuperación de Contraseñas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                            <h6>1. Solicitud</h6>
                                            <p class="small text-muted mb-0">
                                                Usuario ingresa email en formulario de recuperación
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-search fa-2x text-info mb-2"></i>
                                            <h6>2. Validación</h6>
                                            <p class="small text-muted mb-0">
                                                Sistema verifica existencia del email en base de datos
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-paper-plane fa-2x text-success mb-2"></i>
                                            <h6>3. Envío</h6>
                                            <p class="small text-muted mb-0">
                                                Envío de email con token de recuperación temporal
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-lock-open fa-2x text-warning mb-2"></i>
                                            <h6>4. Restablecimiento</h6>
                                            <p class="small text-muted mb-0">
                                                Usuario crea nueva contraseña usando el token
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h6 class="text-secondary">Características del Token</h6>
                                    <ul class="small text-muted">
                                        <li><strong>Duración:</strong> 60 minutos de validez</li>
                                        <li><strong>Uso único:</strong> Se invalida después del uso</li>
                                        <li><strong>Encriptación:</strong> Hash seguro de 64 caracteres</li>
                                        <li><strong>Vinculación:</strong> Asociado al email específico</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-info">Validaciones de Seguridad</h6>
                                    <ul class="small text-muted">
                                        <li>Verificación de token válido y no expirado</li>
                                        <li>Confirmación de email coincidente</li>
                                        <li>Validación de nueva contraseña según políticas</li>
                                        <li>Invalidación de sesiones activas del usuario</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Casos de Error y Manejo -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Casos de Error</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-danger">Errores de Autenticación</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Credenciales Inválidas:</strong> Usuario o contraseña incorrectos</li>
                                <li><strong>Usuario Inactivo:</strong> Cuenta deshabilitada o suspendida</li>
                                <li><strong>Permisos Insuficientes:</strong> Sin acceso a educación inicial</li>
                                <li><strong>Sesión Expirada:</strong> Timeout por inactividad</li>
                                <li><strong>Múltiples Intentos:</strong> Bloqueo temporal por seguridad</li>
                            </ul>

                            <h6 class="text-warning">Mensajes al Usuario</h6>
                            <ul class="small text-muted mb-0">
                                <li>Mensajes genéricos para evitar información sensible</li>
                                <li>Instrucciones claras para resolución</li>
                                <li>Enlaces a recuperación de contraseña</li>
                                <li>Información de contacto para soporte</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-double mr-2"></i>Casos de Éxito</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-success">Flujos Exitosos</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Login Exitoso:</strong> Redirección al dashboard principal</li>
                                <li><strong>Recuperación Exitosa:</strong> Confirmación de nueva contraseña</li>
                                <li><strong>Cambio de Contraseña:</strong> Actualización confirmada</li>
                                <li><strong>Logout Seguro:</strong> Sesión cerrada correctamente</li>
                            </ul>

                            <h6 class="text-info">Datos Disponibles Post-Autenticación</h6>
                            <ul class="small text-muted mb-0">
                                <li>Información completa del usuario</li>
                                <li>Permisos y roles asignados</li>
                                <li>Datos de autoridad educativa</li>
                                <li>Configuraciones personalizadas</li>
                                <li>Historial de acceso reciente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integración con Constructor del Controlador -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-code mr-2"></i>Integración en Controladores</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-info">Constructor del HomeInicialController</h6>
                                    <pre class="small text-muted mb-3"><code>public function __construct()
{
    $this->middleware(['auth','is_inicial', function ($request, $next) {
        $this->user = User::find(Auth::id());
        $this->autoridad = Autoridad::where('user_id',Auth::id())->first();
        $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
        return $next($request);
    }]);
}</code></pre>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">Variables Disponibles</h6>
                                    <ul class="small text-muted mb-3">
                                        <li><strong>$this->user:</strong> Objeto User autenticado</li>
                                        <li><strong>$this->autoridad:</strong> Registro de autoridad</li>
                                        <li><strong>$this->list_comment_autoridad:</strong> Comentarios de campos</li>
                                    </ul>

                                    <h6 class="text-warning">Uso en Métodos</h6>
                                    <pre class="small text-muted mb-0"><code>public function home()
{
    $user = $this->user;
    $autoridad = $this->autoridad;

    return view('inicials.home', compact(
        'user', 'autoridad'
    ));
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mejores Prácticas y Recomendaciones -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-dark">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-lightbulb mr-2"></i>Mejores Prácticas y Recomendaciones</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary"><i class="fas fa-user-shield mr-2"></i>Para Usuarios</h6>
                                    <ul class="small text-muted">
                                        <li>Usar contraseñas fuertes y únicas</li>
                                        <li>No compartir credenciales</li>
                                        <li>Cerrar sesión al terminar</li>
                                        <li>Reportar actividad sospechosa</li>
                                        <li>Mantener datos de contacto actualizados</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fas fa-cogs mr-2"></i>Para Desarrolladores</h6>
                                    <ul class="small text-muted">
                                        <li>Implementar rate limiting</li>
                                        <li>Usar HTTPS en producción</li>
                                        <li>Validar datos en servidor</li>
                                        <li>Mantener logs de seguridad</li>
                                        <li>Actualizar dependencias regularmente</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning"><i class="fas fa-tools mr-2"></i>Para Administradores</h6>
                                    <ul class="small text-muted">
                                        <li>Monitorear intentos de acceso</li>
                                        <li>Revisar logs periódicamente</li>
                                        <li>Configurar alertas de seguridad</li>
                                        <li>Realizar backups de sesiones</li>
                                        <li>Capacitar usuarios en seguridad</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
