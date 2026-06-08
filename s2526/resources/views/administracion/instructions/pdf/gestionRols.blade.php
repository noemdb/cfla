{{-- resources/views/administracion/instructions/pdf/gestionRols.blade.php --}}
<div bgcolor="#ffffff">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#6c757d" align="center" height="80">
                <font size="5" color="#ffffff" face="Arial">
                    <strong>👥 Gestión de Usuarios</strong>
                </font>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e9ecef" align="center" height="40">
                <font size="3" color="#495057" face="Arial">
                    Manual completo de gestión de usuarios, perfiles y roles - Sistema de Modals
                </font>
            </td>
        </tr>
    </table>

    <br>

    <!-- SISTEMA DE PESTAÑAS -->
    <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <!-- Navegación por Pestañas -->
                <table border="0" cellpadding="5" cellspacing="5" width="100%" bgcolor="#e9ecef">
                    <tr>
                        <td width="16%" align="center" bgcolor="#007bff">
                            <font color="#ffffff" face="Arial"><strong>👥 Gestión de Usuarios</strong></font>
                        </td>
                        <td width="17%" align="center" bgcolor="#28a745">
                            <font color="#ffffff" face="Arial"><strong>➕ Creación de Usuarios</strong></font>
                        </td>
                        <td width="16%" align="center">
                            <font color="#000000" face="Arial"><strong>🪪 Gestión de Perfiles</strong></font>
                        </td>
                        <td width="16%" align="center">
                            <font color="#000000" face="Arial"><strong>🛡️ Gestión de Roles</strong></font>
                        </td>
                        <td width="17%" align="center">
                            <font color="#000000" face="Arial"><strong>⚙️ Sistema de Filtros</strong></font>
                        </td>
                        <td width="16%" align="center">
                            <font color="#000000" face="Arial"><strong>❓ Preguntas Frecuentes</strong></font>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- CONTENIDO PESTAÑA USUARIOS -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr>
                        <td>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                                <tr>
                                    <td>
                                        <font size="3" color="#0c5460" face="Arial">
                                            <strong>💡 Información:</strong> En esta sección podrá gestionar todos los
                                            aspectos relacionados con las cuentas de usuario del sistema mediante
                                            <strong>modals interactivos</strong>.
                                        </font>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- BÚSQUEDA Y FILTROS RÁPIDOS -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0"
                                                        bgcolor="#007bff">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial"><strong>🔍</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#007bff" face="Arial"><strong>Búsqueda
                                                            y Filtros Rápidos</strong></font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">El sistema cuenta con funciones de búsqueda
                                            y filtrado avanzado para encontrar usuarios específicos de manera eficiente.
                                        </font>

                                        <br><br>
                                        <font size="3" face="Arial"><strong>Campos de búsqueda
                                                disponibles:</strong></font>
                                        <ul>
                                            <li>
                                                <font size="3" face="Arial"><strong>Nombre de usuario:</strong>
                                                    Busca por el username del sistema</font>
                                            </li>
                                            <li>
                                                <font size="3" face="Arial"><strong>Nombre:</strong> Busca en el
                                                    campo firstname del perfil</font>
                                            </li>
                                            <li>
                                                <font size="3" face="Arial"><strong>Apellido:</strong> Busca en
                                                    el campo lastname del perfil</font>
                                            </li>
                                        </ul>

                                        <table border="1" cellpadding="8" cellspacing="0" width="100%"
                                            bgcolor="#f8f9fa">
                                            <tr>
                                                <td>
                                                    <font size="2" face="Arial"><strong>Flujo:</strong> Campo de
                                                        búsqueda → Escribir término → Botón 🔍 o Enter</font>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                            bgcolor="#fff3cd">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>💡 Consejo:</strong> Puede usar términos parciales. Por
                                                        ejemplo, "mar" encontrará "María", "Mario", "Martín".
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- EDICIÓN DE USUARIOS EN MODAL -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0"
                                                        bgcolor="#ffc107">
                                                        <tr>
                                                            <td>
                                                                <font color="#000000" face="Arial">
                                                                    <strong>✏️</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>Edición de Usuario (Modal)</strong>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Modifique la información básica de la
                                            cuenta de usuario en un <strong>modal centrado</strong> que mantiene el
                                            contexto de la lista.</font>

                                        <br><br>
                                        <table border="0" cellpadding="0" cellspacing="10" width="100%">
                                            <tr>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Campos
                                                            editables:</strong></font>
                                                    <ul>
                                                        <li>
                                                            <font size="3" face="Arial">
                                                                <strong>Username:</strong> Nombre de usuario para login
                                                            </font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial">
                                                                <strong>Email:</strong> Correo electrónico principal
                                                            </font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial">
                                                                <strong>Estado:</strong> Activar/Desactivar cuenta
                                                            </font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial"><strong>Work
                                                                    ID:</strong> Identificador laboral</font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial"><strong>Card
                                                                    ID:</strong> Identificación de tarjeta</font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial">
                                                                <strong>Ident:</strong> Identificación personal
                                                            </font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial"><strong>Number
                                                                    ID:</strong> Número identificador</font>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Validaciones:</strong>
                                                    </font>
                                                    <ul>
                                                        <li>
                                                            <font size="3" face="Arial">Email debe tener
                                                                formato válido y ser único</font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial">Username debe ser
                                                                único</font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial">Work ID, Card ID,
                                                                Ident y Number ID deben ser únicos</font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial">Campos obligatorios
                                                                marcados con *</font>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>

                                        <table border="1" cellpadding="8" cellspacing="0" width="100%"
                                            bgcolor="#f8f9fa">
                                            <tr>
                                                <td>
                                                    <font size="2" face="Arial"><strong>Flujo:</strong> Lista
                                                        usuarios → Botón ✏️ amarillo → Modal de edición → Guardar</font>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                            bgcolor="#fff3cd">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>⚠️ Importante:</strong> Al cambiar el email, se
                                                        actualiza automáticamente en todos los perfiles asociados
                                                        (representante, profesor).
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- SISTEMA DE MODALS -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0"
                                                        bgcolor="#17a2b8">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>🪟</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#0c5460" face="Arial">
                                                        <strong>Sistema de Modals</strong>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Acceda a las diferentes secciones de
                                            gestión mediante <strong>modals superpuestos</strong> que mantienen el foco
                                            en una tarea a la vez.</font>

                                        <br><br>
                                        <table border="0" cellpadding="0" cellspacing="10" width="100%">
                                            <tr>
                                                <td width="25%" valign="top" align="center">
                                                    <table border="1" cellpadding="5" cellspacing="0"
                                                        width="90%" bgcolor="#28a745">
                                                        <tr>
                                                            <td align="center">
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>Verde</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <br>
                                                    <font size="2" face="Arial"><strong>Crear Usuario</strong></font>
                                                    <br>
                                                    <font size="1" color="#6c757d" face="Arial">Modal XL - Asistente completo</font>
                                                </td>
                                                <td width="25%" valign="top" align="center">
                                                    <table border="1" cellpadding="5" cellspacing="0"
                                                        width="90%" bgcolor="#ffc107">
                                                        <tr>
                                                            <td align="center">
                                                                <font color="#000000" face="Arial">
                                                                    <strong>Amarillo</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <br>
                                                    <font size="2" face="Arial"><strong>Editar
                                                            Usuario</strong></font>
                                                    <br>
                                                    <font size="1" color="#6c757d" face="Arial">Modal - Datos
                                                        básicos</font>
                                                </td>
                                                <td width="25%" valign="top" align="center">
                                                    <table border="1" cellpadding="5" cellspacing="0"
                                                        width="90%" bgcolor="#17a2b8">
                                                        <tr>
                                                            <td align="center">
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>Azul</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <br>
                                                    <font size="2" face="Arial"><strong>Gestionar
                                                            Perfil</strong></font>
                                                    <br>
                                                    <font size="1" color="#6c757d" face="Arial">Modal XL -
                                                        Información personal</font>
                                                </td>
                                                <td width="25%" valign="top" align="center">
                                                    <table border="1" cellpadding="5" cellspacing="0"
                                                        width="90%" bgcolor="#28a745">
                                                        <tr>
                                                            <td align="center">
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>Verde</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <br>
                                                    <font size="2" face="Arial"><strong>Administrar
                                                            Roles</strong></font>
                                                    <br>
                                                    <font size="1" color="#6c757d" face="Arial">Modal XL -
                                                        Permisos y accesos</font>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                            bgcolor="#d1ecf1">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#0c5460" face="Arial">
                                                        <strong>Ventajas del sistema de modals:</strong>
                                                    </font>
                                                    <ul>
                                                        <li>
                                                            <font size="3" face="Arial"><strong>Enfoque
                                                                    concentrado:</strong> Una tarea a la vez</font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial"><strong>Contexto
                                                                    preservado:</strong> Lista visible en segundo plano
                                                            </font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial"><strong>Navegación
                                                                    rápida:</strong> Cierre con botón X o clic fuera
                                                            </font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial">
                                                                <strong>Responsive:</strong> Se adapta a todos los
                                                                dispositivos
                                                            </font>
                                                        </li>
                                                        <li>
                                                            <font size="3" face="Arial"><strong>Scroll
                                                                    integrado:</strong> Contenido largo manejable</font>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br><br>

                <!-- NUEVA PESTAÑA: CREACIÓN DE USUARIOS -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr>
                        <td>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d4edda">
                                <tr>
                                    <td>
                                        <font size="3" color="#155724" face="Arial">
                                            <strong>💡 Información:</strong> Cree nuevos usuarios completos con <strong>asistente integrado</strong> que incluye perfil y rol automáticamente.
                                        </font>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- ASISTENTE DE CREACIÓN COMPLETO -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#28a745">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>👤➕</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#155724" face="Arial">
                                                        <strong>Asistente de Creación (Modal XL)</strong>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Cree usuarios completos en un solo proceso con <strong>cuatro secciones integradas</strong> y validación en tiempo real.</font>

                                        <br><br>
                                        <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#f8f9fa">
                                            <tr>
                                                <td>
                                                    <font size="2" face="Arial"><strong>Flujo:</strong> Botón verde "Nuevo" → Modal XL con scroll → Completar 4 secciones → Crear Usuario</font>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d4edda">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#155724" face="Arial"><strong>Proceso automatizado:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial"><strong>Usuario:</strong> Cuenta de acceso al sistema</font></li>
                                                        <li><font size="3" face="Arial"><strong>Perfil:</strong> Información personal automáticamente</font></li>
                                                        <li><font size="3" face="Arial"><strong>Rol:</strong> Permisos y accesos configurados</font></li>
                                                        <li><font size="3" face="Arial"><strong>Transacción segura:</strong> Todo se guarda o nada se guarda</font></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- SECCIÓN 1: DATOS DE ACCESO -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#007bff">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>🔑</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#007bff" face="Arial">
                                                        <strong>Sección 1: Datos de Acceso</strong>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Configure las credenciales de acceso al sistema con validaciones de seguridad.</font>

                                        <br><br>
                                        <table border="0" cellpadding="0" cellspacing="10" width="100%">
                                            <tr>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Campos obligatorios:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial"><strong>Usuario:</strong> Nombre único para login (mín. 3 caracteres)</font></li>
                                                        <li><font size="3" face="Arial"><strong>Email:</strong> Correo válido y único en el sistema</font></li>
                                                        <li><font size="3" face="Arial"><strong>Contraseña:</strong> Mínimo 6 caracteres</font></li>
                                                        <li><font size="3" face="Arial"><strong>Confirmar Contraseña:</strong> Debe coincidir</font></li>
                                                        <li><font size="3" face="Arial"><strong>Estado:</strong> Activo/Inactivo desde creación</font></li>
                                                    </ul>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Validaciones automáticas:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial">Username único en tiempo real</font></li>
                                                        <li><font size="3" face="Arial">Formato de email válido</font></li>
                                                        <li><font size="3" face="Arial">Fortaleza de contraseña</font></li>
                                                        <li><font size="3" face="Arial">Confirmación de contraseña</font></li>
                                                        <li><font size="3" face="Arial">Estado requerido</font></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>🛡️ Seguridad:</strong> La contraseña se encripta automáticamente antes de guardarse.
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- SECCIÓN 2: DATOS DE IDENTIFICACIÓN -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#17a2b8">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>🆔</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#0c5460" face="Arial">
                                                        <strong>Sección 2: Datos de Identificación</strong>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Complete la información de identificación laboral y personal del usuario.</font>

                                        <br><br>
                                        <table border="0" cellpadding="0" cellspacing="10" width="100%">
                                            <tr>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Campos disponibles:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial"><strong>Número de Tarjeta:</strong> Identificación de tarjeta (única)</font></li>
                                                        <li><font size="3" face="Arial"><strong>Ident. Trabajador BIO:</strong> ID biométrico (numérico, único)</font></li>
                                                        <li><font size="3" face="Arial"><strong>Ident. Trabajador:</strong> Identificación laboral (única)</font></li>
                                                    </ul>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Características:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial">Todos los campos son opcionales</font></li>
                                                        <li><font size="3" face="Arial">Validación de unicidad automática</font></li>
                                                        <li><font size="3" face="Arial">Work ID es campo numérico</font></li>
                                                        <li><font size="3" face="Arial">Integración con sistemas biométricos</font></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#0c5460" face="Arial">
                                                        <strong>📊 Biométrico:</strong> El campo "Ident. Trabajador BIO" está optimizado para integración con sistemas de control de acceso.
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- SECCIÓN 3: DATOS PERSONALES -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#28a745">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>👤</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#155724" face="Arial">
                                                        <strong>Sección 3: Datos Personales</strong>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Ingrese la información personal básica para el perfil del usuario.</font>

                                        <br><br>
                                        <table border="0" cellpadding="0" cellspacing="10" width="100%">
                                            <tr>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Campos obligatorios:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial"><strong>Nombres:</strong> Nombre(s) de pila (mín. 2 caracteres)</font></li>
                                                        <li><font size="3" face="Arial"><strong>Apellidos:</strong> Apellido(s) completos (mín. 2 caracteres)</font></li>
                                                    </ul>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Campo opcional:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial"><strong>Cédula:</strong> Número de identificación personal</font></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>📝 Perfil automático:</strong> Esta información crea automáticamente el perfil asociado al usuario.
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- SECCIÓN 4: ASIGNACIÓN DE ROL -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#ffc107">
                                                        <tr>
                                                            <td>
                                                                <font color="#000000" face="Arial">
                                                                    <strong>🛡️</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>Sección 4: Asignación de Rol</strong>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Configure los permisos y accesos del usuario en el sistema.</font>

                                        <br><br>
                                        <table border="0" cellpadding="0" cellspacing="10" width="100%">
                                            <tr>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Campos obligatorios:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial"><strong>Área:</strong> Departamento o sección de trabajo</font></li>
                                                        <li><font size="3" face="Arial"><strong>Rol:</strong> Tipo de permiso y acceso</font></li>
                                                        <li><font size="3" face="Arial"><strong>Fecha Inicial:</strong> Inicio de vigencia</font></li>
                                                        <li><font size="3" face="Arial"><strong>Fecha Final:</strong> Fin de vigencia</font></li>
                                                    </ul>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <font size="3" face="Arial"><strong>Campos opcionales:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial"><strong>Cargo:</strong> Posición específica</font></li>
                                                        <li><font size="3" face="Arial"><strong>Horario:</strong> Asistencia programada</font></li>
                                                        <li><font size="3" face="Arial"><strong>Grupo:</strong> Agrupación de usuarios</font></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#0c5460" face="Arial"><strong>Validaciones de fechas:</strong></font>
                                                    <ul>
                                                        <li><font size="3" face="Arial">Fecha final debe ser igual o posterior a fecha inicial</font></li>
                                                        <li><font size="3" face="Arial">Formato de fecha automático</font></li>
                                                        <li><font size="3" face="Arial">Vigencia configurada automáticamente</font></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- PROCESO DE GUARDADO -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#dc3545">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>💾</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#721c24" face="Arial">
                                                        <strong>Proceso de Guardado Seguro</strong>
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial"><strong>Transacción atómica</strong> que garantiza la integridad de los datos.</font>

                                        <br><br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d4edda">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#155724" face="Arial"><strong>Secuencia de creación:</strong></font>
                                                    <ol>
                                                        <li><font size="3" face="Arial"><strong>Usuario:</strong> Se crea la cuenta de acceso</font></li>
                                                        <li><font size="3" face="Arial"><strong>Perfil:</strong> Se crea el perfil asociado al usuario</font></li>
                                                        <li><font size="3" face="Arial"><strong>Rol:</strong> Se asigna el rol con permisos</font></li>
                                                        <li><font size="3" face="Arial"><strong>Confirmación:</strong> Mensaje de éxito con detalles</font></li>
                                                    </ol>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>⚠️ Garantía de integridad:</strong> Si falla cualquier paso, <strong>ningún dato se guarda</strong>. Evita usuarios sin perfil o sin roles.
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>

                                        <br>
                                        <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#f8f9fa">
                                            <tr>
                                                <td>
                                                    <font size="2" face="Arial"><strong>Flujo:</strong> Botón "Crear Usuario" → Validación → Transacción → Mensaje éxito → Cierre automático</font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br><br>

                <!-- CONTENIDO PESTAÑA PERFILES -->
                <!-- ... (mantener contenido existente de perfiles sin cambios) ... -->

                <br><br>

                <!-- CONTENIDO PESTAÑA ROLES -->
                <!-- ... (mantener contenido existente de roles sin cambios) ... -->

                <br><br>

                <!-- CONTENIDO PESTAÑA FILTROS -->
                <!-- ... (mantener contenido existente de filtros sin cambios) ... -->

                <br><br>

                <!-- CONTENIDO PESTAÑA PREGUNTAS FRECUENTES ACTUALIZADA -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr>
                        <td>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                                <tr>
                                    <td>
                                        <font size="3" color="#0c5460" face="Arial">
                                            <strong>💡 Información:</strong> Encuentre respuestas a las preguntas más
                                            comunes sobre el nuevo sistema de gestión.
                                        </font>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- FAQ 1 - ACTUALIZADA -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#007bff">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>❓</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#007bff" face="Arial"><strong>¿Cómo
                                                            funciona el nuevo sistema de modals?</strong></font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">El sistema de modals ofrece una
                                            experiencia más enfocada:</font>
                                        <ul>
                                            <li>
                                                <font size="3" face="Arial"><strong>Modal de Creación:</strong> Asistente completo en modal XL con scroll</font>
                                            </li>
                                            <li>
                                                <font size="3" face="Arial"><strong>Modal de Edición:</strong>
                                                    Formulario compacto para datos básicos</font>
                                            </li>
                                            <li>
                                                <font size="3" face="Arial"><strong>Modal XL Perfil:</strong>
                                                    Pantalla completa para gestión de información personal</font>
                                            </li>
                                            <li>
                                                <font size="3" face="Arial"><strong>Modal XL Roles:</strong>
                                                    Vista completa con lista y editor integrado</font>
                                            </li>
                                            <li>
                                                <font size="3" face="Arial"><strong>Cierre rápido:</strong>
                                                    Use la X, botón "Cerrar" o clic fuera del modal</font>
                                            </li>
                                        </ul>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                            bgcolor="#fff3cd">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>🎯 Ventaja:</strong> Puede ver la lista de usuarios en
                                                        segundo plano mientras trabaja en un modal.
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- NUEVA FAQ - CREACIÓN DE USUARIOS -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#28a745">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>❓</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#155724" face="Arial"><strong>¿Cómo creo un nuevo usuario completo con perfil y rol?</strong></font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Para crear un usuario completo:</font>
                                        <ol>
                                            <li><font size="3" face="Arial">Haga clic en el botón verde <strong>"Nuevo"</strong> en la parte superior</font></li>
                                            <li><font size="3" face="Arial">Complete la <strong>Sección 1: Datos de Acceso</strong> (usuario, email, contraseña, estado)</font></li>
                                            <li><font size="3" face="Arial">Complete la <strong>Sección 2: Datos de Identificación</strong> (tarjeta, biométrico, identificación)</font></li>
                                            <li><font size="3" face="Arial">Complete la <strong>Sección 3: Datos Personales</strong> (nombres, apellidos, cédula)</font></li>
                                            <li><font size="3" face="Arial">Complete la <strong>Sección 4: Asignación de Rol</strong> (área, rol, fechas, horario)</font></li>
                                            <li><font size="3" face="Arial">Haga clic en <strong>"Crear Usuario"</strong> para guardar todo automáticamente</font></li>
                                        </ol>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#0c5460" face="Arial">
                                                        <strong>🔒 Proceso seguro:</strong> El sistema crea usuario, perfil y rol en una transacción atómica. Si algo falla, no se guarda nada.
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- NUEVA FAQ - CAMPOS DE IDENTIFICACIÓN -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#17a2b8">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>❓</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#0c5460" face="Arial"><strong>¿Qué son los campos de identificación y son obligatorios?</strong></font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <font size="3" face="Arial">Los campos de identificación son opcionales pero útiles para integración:</font>
                                        <ul>
                                            <li><font size="3" face="Arial"><strong>Número de Tarjeta:</strong> Para sistemas de control de acceso con tarjetas</font></li>
                                            <li><font size="3" face="Arial"><strong>Ident. Trabajador BIO:</strong> Para integración con sistemas biométricos (numérico)</font></li>
                                            <li><font size="3" face="Arial"><strong>Ident. Trabajador:</strong> Identificación laboral alternativa</font></li>
                                        </ul>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#856404" face="Arial">
                                                        <strong>⚠️ Importante:</strong> Aunque son opcionales, cada campo debe ser único en el sistema si se proporciona.
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                        <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                                            <tr>
                                                <td>
                                                    <font size="3" color="#0c5460" face="Arial">
                                                        <strong>💡 Recomendación:</strong> Complete al menos uno de estos campos para facilitar la identificación en sistemas externos.
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- FAQ 2 - MANTENIDA -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <table border="1" cellpadding="5" cellspacing="0" bgcolor="#007bff">
                                                        <tr>
                                                            <td>
                                                                <font color="#ffffff" face="Arial">
                                                                    <strong>❓</strong>
                                                                </font>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top" style="padding-left: 10px;">
                                                    <font size="3" color="#007bff" face="Arial"><strong>¿Cómo
                                                            filtro usuarios inactivos en el sistema?</strong></font>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <!-- ... (mantener contenido existente) ... -->
                                    </td>
                                </tr>
                            </table>

                            <!-- ... (mantener el resto de FAQs existentes) ... -->

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

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