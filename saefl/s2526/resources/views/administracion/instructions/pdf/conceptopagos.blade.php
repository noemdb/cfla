<div bgcolor="#ffffff">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#28a745" align="center" height="80">
                <font size="5" color="#ffffff" face="Arial">
                    <strong>■ Gestión de Cuentas de Cobro</strong>
                </font>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e9ecef" align="center" height="40">
                <font size="3" color="#495057" face="Arial">
                    Administración detallada de conceptos individuales de pago dentro del sistema de cuentas por cobrar
                </font>
            </td>
        </tr>
    </table>

    <br>

    <!-- INTRODUCCIÓN -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#28a745" face="Arial">
                    <strong>■ Introducción</strong>
                </font>
                <br><br>
                <font size="3" face="Arial">
                    Este módulo permite <strong>gestionar conceptos individuales de cobro</strong> que componen las
                    cuentas por pagar, definiendo montos específicos, nombres de concepto y estados de asociación.
                    Incluye funcionalidades completas de CRUD con filtros avanzados.
                </font>

                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                    <tr>
                        <td>
                            <font size="3" color="#0c5460" face="Arial">
                                <strong>💡 Jerarquía del sistema:</strong> Plan de Pago → Conceptos de Cobro →
                                <strong>Cuentas de Cobro</strong> → Registros de Pago
                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- 1. FILTRAR CONCEPTOS -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#007bff" face="Arial">
                    <strong>■ 1. Filtrar Cuentas de Cobro</strong>
                </font>

                <br><br>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#e9ecef">
                        <td width="25%">
                            <font face="Arial"><strong>Campo</strong></font>
                        </td>
                        <td width="75%">
                            <font face="Arial"><strong>Uso</strong></font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><kbd>Plan de Pago</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">Filtrar por plan de pago específico</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><kbd>Tipo</kbd></font>
                        </td>
                        <td>
                            <font color="#28a745" face="Arial">● GENERAL</font>
                            <font color="#17a2b8" face="Arial">● INDIVIDUAL</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><kbd>Asociación</kbd></font>
                        </td>
                        <td>
                            <font color="#007bff" face="Arial">● Asociado</font> (con pagos registrados)
                            <font color="#6c757d" face="Arial">● No asociado</font> (sin pagos)
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><kbd>CI Estudiante</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">Buscar por cédula del estudiante (debounce 500ms)</font>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="2" color="#007bff" face="Arial">
                                <strong>Tip:</strong> Use el botón <strong>"Nuevo Concepto"</strong> en el header para
                                crear conceptos directamente desde esta pantalla.
                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- 2. ESTADÍSTICAS -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#28a745" face="Arial">
                    <strong>■ 2. Interpretar estadísticas</strong>
                </font>

                <br><br>
                <table border="0" cellpadding="5" cellspacing="10" width="100%">
                    <tr>
                        <td width="25%" bgcolor="#007bff" align="center">
                            <font color="#ffffff" face="Arial"><strong>Total: X</strong></font>
                            <br>
                            <font size="2" color="#ffffff" face="Arial">Conceptos registrados</font>
                        </td>
                        <td width="25%" bgcolor="#28a745" align="center">
                            <font color="#ffffff" face="Arial"><strong>General: X</strong></font>
                            <br>
                            <font size="2" color="#ffffff" face="Arial">Conceptos de tipo GENERAL</font>
                        </td>
                        <td width="25%" bgcolor="#17a2b8" align="center">
                            <font color="#ffffff" face="Arial"><strong>Individual: X</strong></font>
                            <br>
                            <font size="2" color="#ffffff" face="Arial">Conceptos de tipo INDIVIDUAL</font>
                        </td>
                        <td width="25%" bgcolor="#ffc107" align="center">
                            <font color="#212529" face="Arial"><strong>Total: $X</strong></font>
                            <br>
                            <font size="2" color="#212529" face="Arial">Monto total en USD</font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- 3. INTERPRETAR LA TABLA -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#17a2b8" face="Arial">
                    <strong>■ 3. Interpretar la tabla de conceptos</strong>
                </font>

                <br><br>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#e9ecef">
                        <td width="20%">
                            <font face="Arial"><strong>Columna</strong></font>
                        </td>
                        <td width="80%">
                            <font face="Arial"><strong>Descripción</strong></font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><kbd>ID</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">Identificador único del concepto</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><kbd>Concepto</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">Nombre del concepto</font>
                            <br>
                            <font size="2" color="#6c757d" face="Arial">Tipo (GENERAL/INDIVIDUAL)</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><kbd>Cuenta</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">Nombre de la cuenta padre</font>
                            <br>
                            <font size="2" color="#6c757d" face="Arial">Fecha de vencimiento</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><kbd>Plan de Pago</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">Plan al que pertenece la cuenta</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><kbd>Estudiante (CI)</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">Cédula del estudiante (solo INDIVIDUAL)</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><kbd>Monto USD</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">Valor del concepto en dólares</font>
                        </td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial"><kbd>Estado</kbd></font>
                        </td>
                        <td>
                            <font color="#28a745" face="Arial">● -ASOCIADO-</font> (con pagos)
                            <br>
                            <font color="#6c757d" face="Arial">● -NO ASOCIADO-</font> (sin pagos)
                            <br>
                            <font size="2" color="#6c757d" face="Arial">Estado Activo/Inactivo</font>
                        </td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial"><kbd>Acciones</kbd></font>
                        </td>
                        <td>
                            <font face="Arial">
                                <button
                                    style="background-color: #ffc107; color: black; border: none; padding: 2px 8px; border-radius: 3px; margin: 2px;">✏️</button>
                                Editar
                                <button
                                    style="background-color: #dc3545; color: white; border: none; padding: 2px 8px; border-radius: 3px; margin: 2px;">🗑</button>
                                Eliminar
                            </font>
                            <br>
                            <font size="2" color="#6c757d" face="Arial">Botones habilitados/deshabilitados
                                según permisos</font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- 4. CREAR NUEVOS CONCEPTOS -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#28a745" face="Arial">
                    <strong>■ 4. Crear nuevos Cuentas de Cobro</strong>
                </font>

                <br><br>
                <font size="3" face="Arial"><strong>Procedimiento desde el módulo:</strong></font>

                <br><br>
                <ol>
                    <li>
                        <font size="3" face="Arial">Haga click en <button
                                style="background-color: #28a745; color: white; border: none; padding: 4px 12px; border-radius: 3px;">➕
                                Nuevo Concepto</button> en el header</font>
                    </li>
                    <li>
                        <font size="3" face="Arial">Complete el formulario en el modal:</font>
                        <br><br>
                        <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                            <tr bgcolor="#f8f9fa">
                                <td width="30%">
                                    <font face="Arial"><kbd>Tipo de Cuenta</kbd></font>
                                </td>
                                <td width="70%">
                                    <font face="Arial">
                                        <strong>Select list con opciones:</strong>
                                        <br>
                                        ● GENERAL - Para todos los estudiantes
                                        <br>
                                        ● INDIVIDUAL - Para estudiante específico
                                    </font>
                                </td>
                            </tr>
                            <tr bgcolor="#ffffff">
                                <td>
                                    <font face="Arial"><kbd>Cuenta por Cobrar</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">
                                        <strong>Lista dinámica:</strong>
                                        <br>
                                        ● Para GENERAL: muestra solo cuentas GENERALES
                                        <br>
                                        ● Para INDIVIDUAL: muestra solo cuentas INDIVIDUALES
                                    </font>
                                </td>
                            </tr>
                            <tr bgcolor="#f8f9fa">
                                <td>
                                    <font face="Arial"><kbd>Concepto de Pago</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Seleccione de la lista predefinida de conceptos</font>
                                </td>
                            </tr>
                            <tr bgcolor="#ffffff">
                                <td>
                                    <font face="Arial"><kbd>Monto USD</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Valor en dólares del concepto (numérico, mínimo 0.01)</font>
                                </td>
                            </tr>
                            <tr bgcolor="#f8f9fa">
                                <td>
                                    <font face="Arial"><kbd>Estado</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Activo/Inactivo</font>
                                </td>
                            </tr>
                            <tr bgcolor="#ffffff">
                                <td>
                                    <font face="Arial"><kbd>Descripción</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Detalles adicionales del concepto (obligatorio)</font>
                                </td>
                            </tr>
                            <tr bgcolor="#f8f9fa">
                                <td>
                                    <font face="Arial"><kbd>Observaciones</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Notas internas opcionales</font>
                                </td>
                            </tr>
                            <tr bgcolor="#ffffff">
                                <td>
                                    <font face="Arial"><kbd>Opciones</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">
                                        ● Permite Descuento (checkbox)
                                        <br>
                                        ● Anualidad (checkbox)
                                    </font>
                                </td>
                            </tr>
                        </table>
                    </li>
                    <li>
                        <font size="3" face="Arial">Presione <button
                                style="background-color: #28a745; color: white; border: none; padding: 4px 12px; border-radius: 3px;">💾
                                Crear Concepto</button> para guardar</font>
                    </li>
                </ol>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                    <tr>
                        <td>
                            <font size="3" color="#0c5460" face="Arial">
                                <strong>💡 Característica inteligente:</strong> Al cambiar el tipo (GENERAL/INDIVIDUAL),
                                la lista de "Cuenta por Cobrar" se actualiza automáticamente y se limpia la selección
                                anterior.
                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- 5. EDITAR CONCEPTOS EXISTENTES -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#ffc107" face="Arial">
                    <strong>■ 5. Editar conceptos existentes</strong>
                </font>

                <br><br>
                <font size="3" face="Arial"><strong>Procedimiento:</strong></font>

                <br><br>
                <ol>
                    <li>
                        <font size="3" face="Arial">Haga click en <button
                                style="background-color: #ffc107; color: black; border: none; padding: 2px 8px; border-radius: 3px;">✏️</button>
                            en la columna Acciones</font>
                    </li>
                    <li>
                        <font size="3" face="Arial">Se abrirá el modal de edición con:</font>
                        <br><br>
                        <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                            <tr bgcolor="#f8f9fa">
                                <td width="40%">
                                    <font face="Arial"><kbd>Información de Cuenta</kbd></font>
                                </td>
                                <td width="60%">
                                    <font face="Arial">Datos de solo lectura de la cuenta asociada</font>
                                </td>
                            </tr>
                            <tr bgcolor="#ffffff">
                                <td>
                                    <font face="Arial"><kbd>Concepto de Pago</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Lista desplegable de conceptos</font>
                                </td>
                            </tr>
                            <tr bgcolor="#f8f9fa">
                                <td>
                                    <font face="Arial"><kbd>Monto USD</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Valor editable del concepto</font>
                                </td>
                            </tr>
                            <tr bgcolor="#ffffff">
                                <td>
                                    <font face="Arial"><kbd>Estado</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Activo/Inactivo</font>
                                </td>
                            </tr>
                            <tr bgcolor="#f8f9fa">
                                <td>
                                    <font face="Arial"><kbd>Descripción</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Campo de texto editable</font>
                                </td>
                            </tr>
                            <tr bgcolor="#ffffff">
                                <td>
                                    <font face="Arial"><kbd>Observaciones</kbd></font>
                                </td>
                                <td>
                                    <font face="Arial">Campo de texto opcional</font>
                                </td>
                            </tr>
                        </table>
                    </li>
                    <li>
                        <font size="3" face="Arial">Presione <button
                                style="background-color: #ffc107; color: black; border: none; padding: 4px 12px; border-radius: 3px;">💾
                                Actualizar</button> para guardar cambios</font>
                    </li>
                </ol>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="3" color="#856404" face="Arial">
                                <strong>⚠ Restricción de edición:</strong> Solo conceptos con <code>status_edit =
                                    true</code> pueden ser editados. Los botones estarán deshabilitados para conceptos
                                no editables.
                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- 6. ELIMINAR CONCEPTOS -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#dc3545" face="Arial">
                    <strong>■ 6. Eliminar concepto de cobro</strong>
                </font>

                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="3" color="#856404" face="Arial">
                                <strong>⚠ Restricciones de eliminación:</strong>
                            </font>
                            <br>
                            <ul>
                                <li>
                                    <font size="3" face="Arial">No se puede eliminar si está
                                        <strong>-ASOCIADO-</strong> (tiene pagos registrados)</font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">No se puede eliminar si pertenece a una cuenta
                                        <strong>GENERAL</strong></font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">El botón estará <button
                                            style="background-color: #dc3545; color: white; border: none; padding: 2px 8px; border-radius: 3px;"
                                            disabled>🗑</button> cuando no sea posible eliminar</font>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="3" face="Arial"><strong>Procedimiento:</strong></font>
                <br>
                <ol>
                    <li>
                        <font size="3" face="Arial">Verifique que el estado sea <span
                                style="background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 3px;">-NO
                                ASOCIADO-</span></font>
                    </li>
                    <li>
                        <font size="3" face="Arial">Click en <button
                                style="background-color: #dc3545; color: white; border: none; padding: 2px 8px; border-radius: 3px;">🗑</button>
                            (si está habilitado)</font>
                    </li>
                    <li>
                        <font size="3" face="Arial">Confirme la eliminación en el cuadro de diálogo SweetAlert
                        </font>
                    </li>
                    <li>
                        <font size="3" face="Arial">El sistema mostrará confirmación de eliminación exitosa
                        </font>
                    </li>
                </ol>
            </td>
        </tr>
    </table>

    <br>

    <!-- 7. TIPOS DE CUENTA - GENERAL vs INDIVIDUAL -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#007bff" face="Arial">
                    <strong>■ 7. Tipos de Cuenta: GENERAL vs INDIVIDUAL</strong>
                </font>

                <br><br>
                <table border="0" cellpadding="0" cellspacing="10" width="100%">
                    <tr>
                        <td width="50%" valign="top">
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#28a745">
                                <tr>
                                    <td align="center">
                                        <font color="#ffffff" face="Arial" size="4"><strong>GENERAL</strong>
                                        </font>
                                    </td>
                                </tr>
                            </table>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <font size="3" face="Arial">
                                            <strong>Características:</strong>
                                            <br>• Aplicable a todos los estudiantes
                                            <br>• No se puede eliminar
                                            <br>• Lista específica de cuentas
                                            <br>• Sin estudiante asociado
                                        </font>
                                        <br><br>
                                        <font size="2" color="#28a745" face="Arial">
                                            <strong>Ejemplo:</strong> Matrícula general, Cuota de materiales
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" valign="top">
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#17a2b8">
                                <tr>
                                    <td align="center">
                                        <font color="#ffffff" face="Arial" size="4">
                                            <strong>INDIVIDUAL</strong></font>
                                    </td>
                                </tr>
                            </table>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <font size="3" face="Arial">
                                            <strong>Características:</strong>
                                            <br>• Para estudiante específico
                                            <br>• Se puede eliminar (si no asociado)
                                            <br>• Lista específica de cuentas
                                            <br>• Con CI de estudiante
                                        </font>
                                        <br><br>
                                        <font size="2" color="#17a2b8" face="Arial">
                                            <strong>Ejemplo:</strong> Cuota especial, Pago extraordinario
                                        </font>
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

    <!-- 8. ESTADOS DE ASOCIACIÓN - NUEVA SECCIÓN -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#007bff" face="Arial">
                    <strong>■ 8. Estados de asociación</strong>
                </font>

                <br><br>
                <table border="0" cellpadding="0" cellspacing="10" width="100%">
                    <tr>
                        <td width="50%" valign="top">
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#28a745">
                                <tr>
                                    <td align="center">
                                        <font color="#ffffff" face="Arial" size="4">
                                            <strong>-ASOCIADO-</strong></font>
                                    </td>
                                </tr>
                            </table>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <font size="3" face="Arial">
                                            El concepto tiene <strong>pagos registrados</strong> vinculados a través de
                                            <code>conceptocancelados</code>.
                                            <br><br>
                                            <strong>Características:</strong>
                                            <br>• Tiene pagos registrados
                                            <br>• No se puede eliminar
                                            <br>• Estado de solo lectura
                                            <br>• Datos históricos protegidos
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" valign="top">
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#6c757d">
                                <tr>
                                    <td align="center">
                                        <font color="#ffffff" face="Arial" size="4"><strong>-NO
                                                ASOCIADO-</strong></font>
                                    </td>
                                </tr>
                            </table>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <font size="3" face="Arial">
                                            El concepto <strong>no tiene pagos registrados</strong> y puede ser
                                            eliminado.
                                            <br><br>
                                            <strong>Características:</strong>
                                            <br>• Sin pagos registrados
                                            <br>• Se puede eliminar
                                            <br>• Estado editable
                                            <br>• Disponible para modificación
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
                            <font size="3" color="#007bff" face="Arial">
                                <strong>💡 Nota:</strong> La asociación se crea automáticamente cuando se registra un
                                pago para este concepto a través del sistema de pagos.
                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- 9. NAVEGACIÓN Y CONFIGURACIÓN - NUEVA SECCIÓN -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#6c757d" face="Arial">
                    <strong>■ 9. Navegación y configuración</strong>
                </font>

                <br><br>
                <table border="0" cellpadding="0" cellspacing="10" width="100%">
                    <tr>
                        <td width="50%" valign="top">
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#6c757d">
                                <tr>
                                    <td align="center">
                                        <font color="#ffffff" face="Arial" size="3">
                                            <strong>Paginación</strong></font>
                                    </td>
                                </tr>
                            </table>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <font size="3" face="Arial">
                                            <strong>Funcionalidades:</strong>
                                            <br>• Navegue entre páginas con los controles inferiores
                                            <br>• Los filtros se mantienen al cambiar de página
                                            <br>• Resultados ordenados por fecha de creación
                                            <br>• (más recientes primero)
                                            <br>• Configuración de items por página
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" valign="top">
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#6c757d">
                                <tr>
                                    <td align="center">
                                        <font color="#ffffff" face="Arial" size="3"><strong>Accesos
                                                rápidos</strong></font>
                                    </td>
                                </tr>
                            </table>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <font size="3" face="Arial">
                                            <strong>Navegación entre módulos:</strong>
                                            <br>• Use el menú rápido para ir a otras secciones
                                            <br>• Acceso directo a creación de conceptos
                                            <br>• Navegación a cuentas por pagar relacionadas
                                            <br>• Acceso a planes de pago asociados
                                            <br>• Exportación de reportes
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
                            <font size="3" color="#007bff" face="Arial">
                                <strong>💡 Configuración del sistema:</strong> Los permisos de edición y eliminación
                                están controlados por roles de usuario y las restricciones de negocio definidas en el
                                sistema.
                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- PREGUNTAS FRECUENTES ACTUALIZADAS -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="4" color="#007bff" face="Arial">
                    <strong>■ Preguntas frecuentes</strong>
                </font>

                <br><br>

                <!-- Pregunta 1 - ACTUALIZADA -->
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#007bff" face="Arial">
                                <strong>¿Cómo funciona la selección de tipos GENERAL/INDIVIDUAL?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Al crear un nuevo concepto:
                                <ul>
                                    <li>Seleccione el <strong>tipo</strong> en el dropdown</li>
                                    <li>La lista de <strong>Cuentas por Cobrar</strong> se actualiza automáticamente
                                    </li>
                                    <li>Para GENERAL: solo muestra cuentas de tipo GENERAL</li>
                                    <li>Para INDIVIDUAL: solo muestra cuentas de tipo INDIVIDUAL</li>
                                    <li>La selección anterior se limpia al cambiar de tipo</li>
                                </ul>
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- Pregunta 2 - ACTUALIZADA -->
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#007bff" face="Arial">
                                <strong>¿Por qué no puedo editar un concepto?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Un concepto no puede ser editado cuando:
                                <ul>
                                    <li>El campo <code>status_edit</code> es <code>false</code></li>
                                    <li>El botón de edición estará deshabilitado</li>
                                    <li>Se mostrará el texto "No editable"</li>
                                </ul>
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- Pregunta 3 - ACTUALIZADA -->
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#007bff" face="Arial">
                                <strong>¿Qué significan los estados "Asociado" y "No asociado"?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                <strong>Asociado:</strong> El concepto tiene pagos registrados (relación
                                <code>conceptocancelados</code>).
                                <br>
                                <strong>No asociado:</strong> El concepto no tiene pagos registrados y está disponible
                                para eliminación.
                                <br><br>
                                Esta asociación se crea automáticamente al registrar un pago para el concepto.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- Pregunta 4 - NUEVA -->
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#007bff" face="Arial">
                                <strong>¿Puedo cambiar el tipo de un concepto existente?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                <strong>No,</strong> el tipo de cuenta está determinado por la cuenta padre seleccionada
                                y no puede ser modificado en la edición.
                                <br><br>
                                La información de la cuenta asociada se muestra como <strong>solo lectura</strong> en el
                                modal de edición para mantener la integridad de los datos.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- Pregunta 5 - ACTUALIZADA -->
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#007bff" face="Arial">
                                <strong>¿Qué diferencia hay entre "Conceptos de Cobro" y "Cuentas de Cobro"?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                <strong>Conceptos de Cobro:</strong> Contenedor principal con fechas, descripción, tipo
                                y plan de pago.
                                <br>
                                <strong>Cuentas de Cobro:</strong> Elemento individual dentro de la concepto con monto
                                específico y cuenta definida.
                                <br><br>
                                <strong>Ejemplo:</strong>
                                <br>• "Matrícula 2024" (Cuenta por Pagar)
                                <br>• Contiene: "Inscripción", "Derecho de grado", "Materiales" (Cuentas de Cobro)
                            </font>
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
                    Gestión de Cuentas de Cobro - Control Detallado de Elementos de Cobro | Livewire v2.5
                </font>
            </td>
        </tr>
    </table>

</div>
