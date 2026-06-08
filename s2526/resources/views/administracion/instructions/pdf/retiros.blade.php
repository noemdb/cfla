<div bgcolor="#ffffff">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#007bff" align="center" height="80">
                <font size="6" color="#ffffff" face="Arial">
                    <strong>Gestión de Retiros Estudiantiles</strong>
                </font>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e9ecef" align="center" height="40">
                <font size="4" color="#495057" face="Arial">
                    Gestión segura y controlada de retiros académicos y administrativos
                </font>
            </td>
        </tr>
    </table>

    <br>

    <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
        <tr>
            <td>
                <font size="3" color="#0c5460" face="Arial">
                    <strong>Actualización:</strong> Se ha implementado el campo de observaciones obligatorias para
                    mejorar el control y seguimiento de retiros administrativos.
                </font>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 1: Introducción -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#dc3545" face="Arial">
                    <strong>1. Introducción</strong>
                </font>
                <br><br>
                <font size="3" face="Arial">
                    La <strong>Gestión de Retiro de Estudiantes</strong> es una herramienta especializada que permite
                    gestionar el retiro de estudiantes de manera segura y controlada, diferenciando entre retiro
                    académico y administrativo según el rol del usuario.
                </font>

                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#17a2b8" face="Arial">
                                <strong>Nueva Funcionalidad:</strong>
                            </font>
                            <br>
                            <ul>
                                <li><strong>Observaciones obligatorias</strong> para retiros administrativos</li>
                                <li>Modal nativo para captura de justificaciones</li>
                                <li>Validación en tiempo real (10-500 caracteres)</li>
                                <li>Prevención mejorada de retiros duplicados</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 2: Observaciones Obligatorias -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#28a745" face="Arial">
                    <strong>2. Nuevo: Campo de Observaciones Obligatorias</strong>
                </font>

                <br><br>
                <font size="4" face="Arial"><strong>¿Qué ha cambiado?</strong></font>
                <br>
                <font size="3" face="Arial">
                    Se ha implementado un <strong>campo de observaciones obligatorio</strong> para todos los retiros
                    administrativos, con el objetivo de mejorar el control y seguimiento del proceso.
                </font>

                <br><br>
                <font size="4" face="Arial"><strong>Características del Campo:</strong></font>
                <br>
                <ul>
                    <li><strong>Tipo:</strong> Textarea con validación en tiempo real</li>
                    <li><strong>Longitud:</strong> Mínimo 10 caracteres, máximo 500 caracteres</li>
                    <li><strong>Requerido:</strong> Exclusivamente para retiros administrativos</li>
                    <li><strong>Propósito:</strong> Razonamiento y justificación del retiro</li>
                </ul>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="3" color="#856404" face="Arial">
                                <strong>Importante:</strong> Sin las observaciones completas, <strong>NO se permitirá
                                    procesar</strong> el retiro administrativo. El botón de confirmación permanecerá
                                deshabilitado hasta que se cumplan los requisitos.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Validaciones Implementadas</strong></font>
                <br>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="25%">
                            <font color="#ffffff" face="Arial"><strong>Validación</strong></font>
                        </td>
                        <td width="20%">
                            <font color="#ffffff" face="Arial"><strong>Requisito</strong></font>
                        </td>
                        <td width="35%">
                            <font color="#ffffff" face="Arial"><strong>Mensaje de Error</strong></font>
                        </td>
                        <td width="20%">
                            <font color="#ffffff" face="Arial"><strong>Comportamiento</strong></font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><strong>Campo Requerido</strong></font>
                        </td>
                        <td>
                            <font face="Arial">No puede estar vacío</font>
                        </td>
                        <td>
                            <font face="Arial">"Las observaciones son obligatorias para el retiro administrativo."
                            </font>
                        </td>
                        <td>
                            <font face="Arial">Botón deshabilitado</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><strong>Mínimo de Caracteres</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Al menos 10 caracteres</font>
                        </td>
                        <td>
                            <font face="Arial">"Las observaciones deben tener al menos 10 caracteres."</font>
                        </td>
                        <td>
                            <font face="Arial">Botón deshabilitado</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><strong>Máximo de Caracteres</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Máximo 500 caracteres</font>
                        </td>
                        <td>
                            <font face="Arial">"Las observaciones no pueden exceder los 500 caracteres."</font>
                        </td>
                        <td>
                            <font face="Arial">Bloqueo de escritura</font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 3: Roles y Permisos -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>3. Roles de Usuario y Permisos</strong>
                </font>

                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#e7f3ff" valign="top">
                            <font size="4" color="#007bff" face="Arial">
                                <strong>Rol: Control Académico</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Flujo de Confirmación:</strong></font>
                            <ul>
                                <li><strong>Ventana SweetAlert</strong> tradicional</li>
                                <li><strong>Sin observaciones</strong> obligatorias</li>
                                <li>Confirmación directa con "Sí, continuar"</li>
                            </ul>

                            <font size="3" face="Arial"><strong>Acciones:</strong></font>
                            <ul>
                                <li>Elimina inscripción académica</li>
                                <li>Crea registro de retiro tipo "control"</li>
                                <li>No requiere justificación escrita</li>
                                <li>No genera deudas financieras</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#ffe6e6" valign="top">
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>Rol: Administración</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Nuevo Flujo de Confirmación:</strong></font>
                            <ul>
                                <li><strong>Modal nativo</strong> con textarea</li>
                                <li><strong>Observaciones obligatorias</strong> (10-500 chars)</li>
                                <li>Validación en tiempo real</li>
                                <li>Botón habilitado solo con datos válidos</li>
                            </ul>

                            <font size="3" face="Arial"><strong>Acciones:</strong></font>
                            <ul>
                                <li>Genera deuda pendiente si aplica</li>
                                <li>Actualiza datos administrativos</li>
                                <li>Guarda observaciones en el registro</li>
                                <li>Requiere justificación escrita completa</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 4: Inicio Rápido -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#ffc107" face="Arial">
                    <strong>4. Inicio Rápido - Flujos por Rol</strong>
                </font>

                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#e7f3ff" valign="top">
                            <font size="4" color="#007bff" face="Arial">
                                <strong>Control Académico - 3 Pasos</strong>
                            </font>
                            <br><br>
                            <ol>
                                <li><strong>Buscar Estudiante</strong> - Por nombre o cédula</li>
                                <li><strong>Confirmar en SweetAlert</strong> - Ventana emergente</li>
                                <li><strong>Retiro Procesado</strong> - Inscripción eliminada</li>
                            </ol>
                            <br>
                            <center>
                                <font face="Arial" color="#007bff"><strong>Flujo Rápido</strong></font>
                            </center>
                        </td>
                        <td width="50%" bgcolor="#ffe6e6" valign="top">
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>Administración - 4 Pasos</strong>
                            </font>
                            <br><br>
                            <ol>
                                <li><strong>Buscar Estudiante</strong> - Por nombre o cédula</li>
                                <li><strong>Modal de Observaciones</strong> - Justificación obligatoria</li>
                                <li><strong>Validar y Confirmar</strong> - Botón inteligente</li>
                                <li><strong>Retiro Procesado</strong> - Con deuda si aplica</li>
                            </ol>
                            <br>
                            <center>
                                <font face="Arial" color="#dc3545"><strong>Con Validación</strong></font>
                            </center>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 5: Paso 1 - Búsqueda -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>5. Paso 1: Búsqueda y Selección de Estudiantes</strong>
                </font>

                <br><br>
                <font size="4" face="Arial"><strong>Procedimiento de Búsqueda</strong></font>
                <br>
                <ol>
                    <li><strong>Acceder a la Gestión</strong> - Se muestra lista paginada de estudiantes activos</li>
                    <li>
                        <strong>Utilizar buscador:</strong>
                        <ul>
                            <li>Escribir nombre completo o parcial del estudiante</li>
                            <li>O ingresar número de cédula</li>
                            <li>Busca en tiempo real (500ms de delay)</li>
                        </ul>
                    </li>
                    <li><strong>Filtrar resultados</strong> - Seleccionar cantidad de registros a mostrar (10, 25, 50,
                        100)</li>
                    <li><strong>Identificar estudiante</strong> en la tabla de resultados</li>
                </ol>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#17a2b8" face="Arial">
                                <strong>Consejo de Búsqueda:</strong> Use solo el primer nombre o primer apellido para
                                obtener mejores resultados. Se busca coincidencias parciales en nombre, apellido y
                                cédula.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Interpretación de la Tabla de Resultados</strong></font>
                <br>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="20%">
                            <font color="#ffffff" face="Arial"><strong>Columna</strong></font>
                        </td>
                        <td width="25%">
                            <font color="#ffffff" face="Arial"><strong>Descripción</strong></font>
                        </td>
                        <td width="20%">
                            <font color="#ffffff" face="Arial"><strong>Indicadores Clave</strong></font>
                        </td>
                        <td width="35%">
                            <font color="#ffffff" face="Arial"><strong>Significado</strong></font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><strong>Estudiante</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Nombre completo</font>
                        </td>
                        <td>
                            <font face="Arial">Color del texto</font>
                        </td>
                        <td>
                            <font color="#dc3545" face="Arial">Rojo = Deuda pendiente</font><br>
                            <font color="#000000" face="Arial">Negro = Sin deuda</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><strong>Plan de Pago</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Plan asignado</font>
                        </td>
                        <td>
                            <font face="Arial">Badge de color</font>
                        </td>
                        <td>
                            <font face="Arial">Muestra plan actual o "NINGUNO"</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><strong>Deuda [USD]</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Monto adeudado</font>
                        </td>
                        <td>
                            <font face="Arial">Número formateado</font>
                        </td>
                        <td>
                            <font face="Arial">0.00 = Solvente<br>>0.00 = Con deuda</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><strong>R.Académico</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Retiro académico</font>
                        </td>
                        <td>
                            <font face="Arial">Badge naranja/gris</font>
                        </td>
                        <td>
                            <font face="Arial">SI = Con retiro<br>NO = Sin retiro</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><strong>R.Administrativo</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Retiro administrativo</font>
                        </td>
                        <td>
                            <font face="Arial">Badge rojo/gris</font>
                        </td>
                        <td>
                            <font face="Arial">SI = Con retiro<br>NO = Sin retiro</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><strong>Acción</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Botón de retiro</font>
                        </td>
                        <td>
                            <font face="Arial">Color del botón</font>
                        </td>
                        <td>
                            <font color="#dc3545" face="Arial">Rojo = Disponible</font><br>
                            <font color="#6c757d" face="Arial">Gris = No disponible</font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 6: Paso 2 - Confirmación -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>6. Paso 2: Confirmación del Retiro</strong>
                </font>

                <br><br>
                <font size="4" face="Arial"><strong>Nuevos Procesos de Confirmación</strong></font>
                <br><br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#e7f3ff" valign="top">
                            <font size="4" color="#007bff" face="Arial">
                                <strong>Control Académico (Existente)</strong>
                            </font>
                            <br><br>
                            <ol>
                                <li><strong>Click en botón rojo</strong> del estudiante</li>
                                <li><strong>SweetAlert de confirmación</strong> aparece</li>
                                <li><strong>Revisar información</strong> del estudiante</li>
                                <li><strong>Confirmar con "Sí, continuar"</strong></li>
                                <li><strong>Retiro procesado</strong> inmediatamente</li>
                            </ol>

                            <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                                <tr>
                                    <td>
                                        <font size="2" face="Arial">
                                            <strong>Características:</strong> Proceso rápido y directo, sin captura de
                                            observaciones, confirmación tradicional
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" bgcolor="#ffe6e6" valign="top">
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>Administración (Nuevo)</strong>
                            </font>
                            <br><br>
                            <ol>
                                <li><strong>Click en botón rojo</strong> del estudiante</li>
                                <li><strong>Modal nativo aparece</strong> con textarea</li>
                                <li><strong>Ingresar observaciones</strong> (10-500 chars)</li>
                                <li><strong>Botón se habilita</strong> automáticamente</li>
                                <li><strong>Confirmar retiro</strong> con justificación</li>
                            </ol>

                            <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#f8d7da">
                                <tr>
                                    <td>
                                        <font size="2" face="Arial">
                                            <strong>Nuevas Características:</strong> Validación en tiempo real, contador
                                            de caracteres dinámico, botón inteligente
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Características del Nuevo Modal</strong></font>
                <br><br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="33%" bgcolor="#e7f3ff" align="center">
                            <font size="3" face="Arial"><strong>Contador en Tiempo Real</strong></font>
                            <br>
                            <font size="2" face="Arial">Muestra 0/500 → 150/500</font>
                            <br><br>
                            <font color="#28a745" face="Arial">✓ Válido</font> -
                            <font color="#dc3545" face="Arial">✗ Inválido</font>
                        </td>
                        <td width="34%" bgcolor="#fff3cd" align="center">
                            <font size="3" face="Arial"><strong>Botón Inteligente</strong></font>
                            <br>
                            <font size="2" face="Arial">Se habilita solo con datos válidos</font>
                            <br><br>
                            <font color="#28a745" face="Arial">Habilitado</font> -
                            <font color="#6c757d" face="Arial">Deshabilitado</font>
                        </td>
                        <td width="33%" bgcolor="#ffe6e6" align="center">
                            <font size="3" face="Arial"><strong>Bloqueo por Retiro Existente</strong></font>
                            <br>
                            <font size="2" face="Arial">Campos deshabilitados si ya tiene retiro</font>
                            <br><br>
                            <font color="#ffc107" face="Arial">Bloqueado</font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 7: Paso 3 - Procesamiento -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>7. Paso 3: Procesamiento con Observaciones</strong>
                </font>

                <br><br>
                <font size="4" face="Arial"><strong>Mejoras en el Registro de Retiros</strong></font>
                <br><br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#e7f3ff" valign="top">
                            <font size="4" color="#007bff" face="Arial">
                                <strong>Retiro Académico (Control)</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Acciones Ejecutadas:</strong></font>
                            <ol>
                                <li><strong>Registro de Retiro:</strong> Crea/actualiza registro tipo "control"</li>
                                <li><strong>Eliminación de Inscripción:</strong> Remueve inscripción académica</li>
                                <li><strong>Estado:</strong> Marca status_control = 'true'</li>
                            </ol>
                        </td>
                        <td width="50%" bgcolor="#ffe6e6" valign="top">
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>Retiro Administrativo (Admon)</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Acciones Ejecutadas:</strong></font>
                            <ol>
                                <li><strong>Registro de Retiro:</strong> Crea/actualiza registro tipo "admon"</li>
                                <li><strong>Gestión de Deudas:</strong> Genera deuda pendiente si existe</li>
                                <li><strong>Actualización Administrativa:</strong> Cambia plan a "D. RETIRO
                                    ADMINISTRATIVO"</li>
                                <li><strong>Estado:</strong> Marca status_admon = 'true'</li>
                                <li><strong>Observaciones:</strong> Guarda justificación permanentemente</li>
                            </ol>

                            <br>
                            <table border="1" cellpadding="5" cellspacing="0" width="100%" bgcolor="#d4edda">
                                <tr>
                                    <td>
                                        <font size="2" color="#155724" face="Arial">
                                            <strong>Nuevo:</strong> Las observaciones ahora se guardan permanentemente
                                            en el registro del retiro.
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Procesamiento de Deudas (Solo Administrativo)</strong>
                </font>
                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                    <tr>
                        <td>
                            <font size="3" color="#0c5460" face="Arial">
                                <strong>Generación Automática de Deuda:</strong> Si el estudiante tiene deudas vencidas
                                > 0:
                            </font>
                            <ul>
                                <li><strong>Crea cuenta por pagar</strong> en plan "D. RETIRO ADMINISTRATIVO"</li>
                                <li><strong>Genera concepto de pago</strong> por el monto adeudado</li>
                                <li><strong>Fecha de vencimiento:</strong> Fecha actual</li>
                                <li><strong>Descripción:</strong> Incluye usuario que realizó el retiro</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 8: Resultados y Confirmación -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>8. Resultados y Confirmación Final</strong>
                </font>

                <br><br>
                <font size="4" face="Arial"><strong>Mensajes de Resultado</strong></font>
                <br><br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#d4edda" valign="top">
                            <font size="4" color="#155724" face="Arial">
                                <strong>Éxito</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Mensaje:</strong> "El estudiante [Nombre] ha
                                sido retirado exitosamente."</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Indicadores:</strong></font>
                            <ul>
                                <li>Ventana verde con icono de verificación</li>
                                <li>Estudiante desaparece de la lista o muestra retiro</li>
                                <li>Botón cambia a gris deshabilitado</li>
                                <li>Badge correspondiente cambia a "SI"</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#f8d7da" valign="top">
                            <font size="4" color="#721c24" face="Arial">
                                <strong>Error</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Mensaje:</strong> Describe el error específico
                                ocurrido</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Causas comunes:</strong></font>
                            <ul>
                                <li>Problemas de conexión a base de datos</li>
                                <li>Estudiante no encontrado</li>
                                <li>Permisos insuficientes</li>
                                <li>Error en transacción</li>
                                <li>Validación de observaciones fallida</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Cambios Visuales Post-Retiro</strong></font>
                <br>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="25%">
                            <font color="#ffffff" face="Arial"><strong>Elemento</strong></font>
                        </td>
                        <td width="35%">
                            <font color="#ffffff" face="Arial"><strong>Cambio Visual</strong></font>
                        </td>
                        <td width="40%">
                            <font color="#ffffff" face="Arial"><strong>Significado</strong></font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><strong>Botón de Acción</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Rojo → Gris deshabilitado</font>
                        </td>
                        <td>
                            <font face="Arial">Retiro ya realizado</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><strong>Badge R.Académico</strong></font>
                        </td>
                        <td>
                            <font face="Arial">NO → SI (naranja)</font>
                        </td>
                        <td>
                            <font face="Arial">Retiro académico registrado</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><strong>Badge R.Administrativo</strong></font>
                        </td>
                        <td>
                            <font face="Arial">NO → SI (rojo)</font>
                        </td>
                        <td>
                            <font face="Arial">Retiro administrativo registrado</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><strong>Columna Fecha</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Aparece fecha/hora</font>
                        </td>
                        <td>
                            <font face="Arial">Momento exacto del retiro</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><strong>Observaciones</strong></font>
                        </td>
                        <td>
                            <font face="Arial">Guardadas en BD</font>
                        </td>
                        <td>
                            <font face="Arial">Justificación disponible para auditoría</font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 9: Casos Especiales -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#ffc107" face="Arial">
                    <strong>9. Casos Especiales y Validaciones</strong>
                </font>

                <br><br>
                <font size="4" face="Arial"><strong>Nuevos Escenarios de Validación</strong></font>
                <br><br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#d4edda" valign="top">
                            <font size="4" color="#155724" face="Arial">
                                <strong>Observaciones Válidas</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Condiciones:</strong> 10-500 caracteres, texto
                                significativo</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Resultado:</strong> Botón de confirmación
                                HABILITADO</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Indicador:</strong> Contador en verde ✓</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Ejemplo válido:</strong> "Estudiante se retira
                                por cambio de ciudad. Los padres presentaron documentación de traslado familiar."</font>
                        </td>
                        <td width="50%" bgcolor="#fff3cd" valign="top">
                            <font size="4" color="#856404" face="Arial">
                                <strong>Observaciones Inválidas</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Condiciones:</strong> Menos de 10 chars o más
                                de 500</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Resultado:</strong> Botón de confirmación
                                DESHABILITADO</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Indicador:</strong> Contador en rojo ✗</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Ejemplo inválido:</strong> "Se retira" (muy
                                corto)</font>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#d1ecf1" valign="top">
                            <font size="4" color="#0c5460" face="Arial">
                                <strong>Retiro Administrativo Existente</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Comportamiento:</strong> Modal muestra campos
                                DESHABILITADOS</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Textarea:</strong> No editable</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Botón confirmar:</strong> Deshabilitado
                                permanentemente</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Mensaje:</strong> "No se puede procesar el
                                retiro: ya tiene retiro administrativo"</font>
                        </td>
                        <td width="50%" bgcolor="#e2e3e5" valign="top">
                            <font size="4" color="#383d41" face="Arial">
                                <strong>Cierre y Reapertura</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Cancelar modal:</strong> Cierra y limpia
                                observaciones</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Reabrir:</strong> Comienza con textarea vacío
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Persistencia:</strong> No guarda datos hasta
                                confirmación</font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Seguridad:</strong> No permite confirmación
                                sin validación</font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Escenarios Comunes Adicionales</strong></font>
                <br><br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="33%" bgcolor="#e7f3ff" valign="top">
                            <font size="3" color="#007bff" face="Arial">
                                <strong>Estudiante con Deuda</strong>
                            </font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Comportamiento:</strong> Nombre en rojo,
                                retiro administrativo genera deuda</font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Acción:</strong> Sistema crea registro de
                                deuda automáticamente</font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Plan destino:</strong> "D. RETIRO
                                ADMINISTRATIVO"</font>
                        </td>
                        <td width="34%" bgcolor="#fff3cd" valign="top">
                            <font size="3" color="#856404" face="Arial">
                                <strong>Sin Plan de Pago</strong>
                            </font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Indicador:</strong> Badge gris "NINGUNO"
                            </font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Retiro administrativo:</strong> Asigna plan
                                "D. RETIRO ADMINISTRATIVO"</font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Retiro académico:</strong> No afecta planes
                            </font>
                        </td>
                        <td width="33%" bgcolor="#d4edda" valign="top">
                            <font size="3" color="#155724" face="Arial">
                                <strong>Estudiante Solvente</strong>
                            </font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Indicador:</strong> Nombre en negro, deuda
                                0.00</font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Retiro administrativo:</strong> No genera
                                deudas</font>
                            <br><br>
                            <font size="2" face="Arial"><strong>Proceso:</strong> Más rápido y simple</font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 10: Preguntas Frecuentes -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>10. Preguntas Frecuentes Actualizadas</strong>
                </font>

                <br><br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="4" color="#007bff" face="Arial">
                                <strong>¿Por qué ahora debo ingresar observaciones para retiros
                                    administrativos?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                <strong>Mejora en el control y seguimiento.</strong> Las observaciones obligatorias
                                permiten:
                            </font>
                            <ul>
                                <li>Documentar el razonamiento detrás de cada retiro administrativo</li>
                                <li>Crear un historial auditable de justificaciones</li>
                                <li>Mejorar la transparencia en los procesos administrativos</li>
                                <li>Proveer contexto para futuras consultas o auditorías</li>
                                <li>Cumplir con estándares de buenas prácticas en gestión educativa</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="4" color="#856404" face="Arial">
                                <strong>¿Qué tipo de información debo incluir en las observaciones?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Información relevante y específica:</strong>
                            </font>
                            <ul>
                                <li><strong>Razón principal:</strong> "Retiro por transferencia a otra institución"</li>
                                <li><strong>Contexto:</strong> "Estudiante con deuda pendiente del período anterior"
                                </li>
                                <li><strong>Justificación:</strong> "Solicitud de padres por cambio de residencia"</li>
                                <li><strong>Detalles importantes:</strong> "Acuerdo de pago pendiente establecido"</li>
                            </ul>
                            <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                                <tr>
                                    <td>
                                        <font size="2" face="Arial">
                                            <strong>Ejemplo de observaciones válidas:</strong> "El estudiante se retira
                                            por cambio de ciudad. Los padres presentaron documentación que avala el
                                            traslado familiar. Se genera deuda pendiente por el saldo del período
                                            actual."
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                    <tr>
                        <td>
                            <font size="4" color="#0c5460" face="Arial">
                                <strong>¿Qué pasa si el estudiante ya tiene un retiro administrativo
                                    registrado?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>El sistema previene retiros
                                    duplicados:</strong></font>
                            <ul>
                                <li>El modal se abre pero muestra un mensaje de advertencia</li>
                                <li>El campo de observaciones aparece DESHABILITADO</li>
                                <li>El botón de confirmar aparece DESHABILITADO</li>
                                <li>Se muestra la fecha del retiro anterior</li>
                            </ul>
                            <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#fff3cd">
                                <tr>
                                    <td>
                                        <font size="2" face="Arial">
                                            <strong>Acción requerida:</strong> No es posible procesar un nuevo retiro
                                            administrativo para un estudiante que ya tiene uno registrado. Contacte al
                                            administrador del sistema si esto representa un error.
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="4" color="#007bff" face="Arial">
                                <strong>¿Puedo guardar las observaciones y completarlas después?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>No, el proceso es secuencial y en una sola
                                    sesión:</strong></font>
                            <ul>
                                <li>Las observaciones se capturan justo antes de la confirmación</li>
                                <li>Si cierra el modal, las observaciones se pierden</li>
                                <li>Debe completar todo el proceso en una sola interacción</li>
                                <li>Prepare la justificación antes de iniciar el retiro</li>
                            </ul>
                            <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                                <tr>
                                    <td>
                                        <font size="2" face="Arial">
                                            <strong>Recomendación:</strong> Tenga preparada la justificación del retiro
                                            antes de hacer clic en el botón de retirar. Esto agiliza el proceso y
                                            asegura que capture toda la información relevante.
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="4" color="#856404" face="Arial">
                                <strong>¿Dónde puedo ver las observaciones de retiros anteriores?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Las observaciones se almacenan en el registro
                                    del retiro:</strong></font>
                            <ul>
                                <li>En la tabla de retiros en la base de datos</li>
                                <li>En reportes del sistema de administración</li>
                                <li>En consultas históricas de estudiantes</li>
                                <li>En auditorías del sistema</li>
                            </ul>
                            <font size="3" face="Arial">Para acceder a observaciones de retiros anteriores,
                                consulte los módulos de reportes o contacte al administrador del sistema.</font>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffe6e6">
                    <tr>
                        <td>
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>¿Por qué el botón de confirmar no se habilita?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Causas comunes del botón
                                    deshabilitado:</strong></font>
                            <br><br>
                            <table border="1" cellpadding="6" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr bgcolor="#343a40">
                                    <td width="40%">
                                        <font color="#ffffff" face="Arial"><strong>Causa</strong></font>
                                    </td>
                                    <td width="35%">
                                        <font color="#ffffff" face="Arial"><strong>Solución</strong></font>
                                    </td>
                                    <td width="25%">
                                        <font color="#ffffff" face="Arial"><strong>Indicador</strong></font>
                                    </td>
                                </tr>
                                <tr bgcolor="#f8f9fa">
                                    <td>
                                        <font face="Arial"><strong>Observaciones muy cortas</strong></font>
                                    </td>
                                    <td>
                                        <font face="Arial">Escriba al menos 10 caracteres</font>
                                    </td>
                                    <td>
                                        <font color="#dc3545" face="Arial">✗ Contador rojo</font>
                                    </td>
                                </tr>
                                <tr bgcolor="#ffffff">
                                    <td>
                                        <font face="Arial"><strong>Observaciones muy largas</strong></font>
                                    </td>
                                    <td>
                                        <font face="Arial">Reduzca a máximo 500 caracteres</font>
                                    </td>
                                    <td>
                                        <font color="#dc3545" face="Arial">✗ Contador rojo</font>
                                    </td>
                                </tr>
                                <tr bgcolor="#f8f9fa">
                                    <td>
                                        <font face="Arial"><strong>Retiro administrativo existente</strong></font>
                                    </td>
                                    <td>
                                        <font face="Arial">No puede procesar nuevo retiro</font>
                                    </td>
                                    <td>
                                        <font color="#6c757d" face="Arial">Bloqueado</font>
                                    </td>
                                </tr>
                                <tr bgcolor="#ffffff">
                                    <td>
                                        <font face="Arial"><strong>Campo vacío</strong></font>
                                    </td>
                                    <td>
                                        <font face="Arial">Ingrese las observaciones requeridas</font>
                                    </td>
                                    <td>
                                        <font color="#ffc107" face="Arial">0/500</font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Sección 11: Resumen de Mejoras -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#ffc107" face="Arial">
                    <strong>11. Resumen de Mejoras Implementadas</strong>
                </font>

                <br><br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#d4edda" valign="top">
                            <font size="4" color="#155724" face="Arial">
                                <strong>Nuevas Funcionalidades</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>Observaciones obligatorias</strong> para retiros administrativos</li>
                                <li><strong>Modal nativo</strong> con validación en tiempo real</li>
                                <li><strong>Contador de caracteres</strong> dinámico (0/500 → 150/500)</li>
                                <li><strong>Botón inteligente</strong> que se habilita automáticamente</li>
                                <li><strong>Bloqueo por retiros duplicados</strong> mejorado</li>
                                <li><strong>Almacenamiento permanente</strong> de justificaciones</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#d1ecf1" valign="top">
                            <font size="4" color="#0c5460" face="Arial">
                                <strong>Mejoras de Control</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>Auditoría mejorada</strong> con justificaciones documentadas</li>
                                <li><strong>Prevención de errores</strong> con validación estricta</li>
                                <li><strong>Transparencia</strong> en procesos administrativos</li>
                                <li><strong>Historial completo</strong> de razones de retiro</li>
                                <li><strong>Seguridad</strong> contra retiros inconsistentes</li>
                                <li><strong>Calidad de datos</strong> con información contextual</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d4edda">
                    <tr>
                        <td>
                            <font size="3" color="#155724" face="Arial">
                                <strong>Beneficios para los Usuarios:</strong> Estas mejoras proporcionan un mejor
                                control sobre el proceso de retiros, documentación completa de las decisiones
                                administrativas, y prevención de errores mediante validaciones en tiempo real.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td align="center">
                            <font size="4" color="#007bff" face="Arial">
                                <strong>¿Necesita más ayuda?</strong>
                            </font>
                            <br>
                            <font size="3" face="Arial">Contacte al administrador del sistema o consulte la
                                documentación técnica para información adicional.</font>
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
                    Guía del Sistema de Retiros Estudiantiles - Versión 2.0 - Actualizado con Observaciones Obligatorias
                </font>
            </td>
        </tr>
    </table>

</div>
