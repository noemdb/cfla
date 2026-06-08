<div bgcolor="#ffffff">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#6c757d" align="center" height="80">
                <font size="5" color="#ffffff" face="Arial">
                    <strong><i>■</i> Gestión de Anulación de Pagos</strong>
                </font>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e9ecef" align="center" height="40">
                <font size="3" color="#495057" face="Arial">
                    Proceso administrativo para habilitar / reversar anulaciones de recibos de pago
                </font>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f8f9fa" align="center" height="30">
                <font size="2" color="#6c757d" face="Arial">
                    Tiempo estimado de lectura: 4 min
                </font>
            </td>
        </tr>
    </table>

    <br>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td width="100%" valign="top">

                <!-- INTRODUCCIÓN -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
                    <tr>
                        <td>
                            <font size="4" color="#dc3545" face="Arial">
                                <strong><i>■</i> Introducción</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Esta pantalla permite <strong>auditar</strong> y <strong>autorizar</strong> futuras
                                anulaciones de recibos antes de que se ejecuten, garantizando trazabilidad y control
                                administrativo.
                            </font>

                            <br><br>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                                <tr>
                                    <td>
                                        <font size="3" color="#0c5460" face="Arial">
                                            <strong><i>💡</i> ¿Por qué "marcar anulable"?</strong> Evita anulaciones
                                            accidentales; solo los usuarios autorizados podrán anular después de esta
                                            habilitación.
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- 1. FILTRAR -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
                    <tr>
                        <td>
                            <font size="4" color="#007bff" face="Arial">
                                <strong><i>■</i> 1. Filtrar pagos</strong>
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
                                        <font face="Arial"><kbd>Buscar</kbd></font>
                                    </td>
                                    <td>
                                        <font face="Arial">Estudiante, cédula, representante o concepto.</font>
                                    </td>
                                </tr>
                                <tr bgcolor="#f8f9fa">
                                    <td>
                                        <font face="Arial"><kbd>Fecha inicio / fin</kbd></font>
                                    </td>
                                    <td>
                                        <font face="Arial">Rango de registro de pago.</font>
                                    </td>
                                </tr>
                                <tr bgcolor="#ffffff">
                                    <td>
                                        <font face="Arial"><kbd>Estado</kbd></font>
                                    </td>
                                    <td>
                                        <font color="#28a745" face="Arial">● Activo</font>
                                        <font color="#ffc107" face="Arial">● Anulable</font>
                                        <font color="#dc3545" face="Arial">● Anulado</font>
                                    </td>
                                </tr>
                            </table>

                            <br>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                                <tr>
                                    <td>
                                        <font size="2" color="#007bff" face="Arial">
                                            <strong>Tip:</strong> Use <kbd>Ctrl</kbd> + <kbd>F</kbd> para saltar al
                                            cuadro de búsqueda.
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- 2. REVISAR -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
                    <tr>
                        <td>
                            <font size="4" color="#17a2b8" face="Arial">
                                <strong><i>■</i> 2. Revisar detalle del pago</strong>
                            </font>

                            <br><br>
                            <font size="3" face="Arial">
                                Presione <button
                                    style="background-color: #17a2b8; color: white; border: none; padding: 2px 8px; border-radius: 3px;"><i>ℹ</i></button>
                                para abrir modal con:
                            </font>

                            <br><br>
                            <ul>
                                <li>
                                    <font size="3" face="Arial">Datos del estudiante y representante</font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">Concepto y monto cancelado</font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">Usuario que registró el pago</font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">Estado actual (activo / anulado / anulable)
                                    </font>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- 3. MARCAR ANULABLE -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
                    <tr>
                        <td>
                            <font size="4" color="#ffc107" face="Arial">
                                <strong><i>■</i> 3. Marcar pago como "anulable"</strong>
                            </font>

                            <br><br>
                            <ol>
                                <li>
                                    <font size="3" face="Arial">Click en <button
                                            style="background-color: #ffc107; color: black; border: none; padding: 2px 8px; border-radius: 3px;"><i>🔓</i></button>
                                    </font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">Confirme estudiante y concepto.</font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">Escriba <strong>justificación</strong> (10-500
                                        caracteres).</font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">Presione <span
                                            style="background-color: #ffc107; color: black; padding: 2px 6px; border-radius: 3px;">Marcar
                                            como anulable</span>.</font>
                                </li>
                            </ol>

                            <br>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#fff3cd">
                                <tr>
                                    <td>
                                        <font size="3" color="#856404" face="Arial">
                                            El pago ahora aparece con etiqueta <span
                                                style="background-color: #ffc107; color: black; padding: 2px 6px; border-radius: 3px;">Anulable</span>
                                            y puede ser anulado desde la sección "Listado de pagos registrados".
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- 4. REVERTIR -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
                    <tr>
                        <td>
                            <font size="4" color="#6c757d" face="Arial">
                                <strong><i>■</i> 4. Quitar estado "anulable"</strong>
                            </font>

                            <br><br>
                            <font size="3" face="Arial">Si se equivocó o ya no desea permitir la anulación:
                            </font>

                            <br><br>
                            <ul>
                                <li>
                                    <font size="3" face="Arial">Click en <button
                                            style="background-color: #6c757d; color: white; border: none; padding: 2px 8px; border-radius: 3px;"><i>🔒</i></button>
                                    </font>
                                </li>
                                <li>
                                    <font size="3" face="Arial">Confirme la acción en el cuadro de diálogo.
                                    </font>
                                </li>
                            </ul>

                            <br>
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#e2e3e5">
                                <tr>
                                    <td>
                                        <font size="2" color="#383d41" face="Arial">
                                            No disponible si el pago ya fue anulado.
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <!-- PREGUNTAS FRECUENTES -->
                <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
                    <tr>
                        <td>
                            <font size="4" color="#007bff" face="Arial">
                                <strong><i>■</i> Preguntas frecuentes</strong>
                            </font>

                            <br><br>

                            <!-- Pregunta 1 -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#e7f3ff">
                                <tr>
                                    <td>
                                        <font size="3" color="#007bff" face="Arial">
                                            <strong>¿Puedo anular directamente desde esta pantalla?</strong>
                                        </font>
                                        <br><br>
                                        <font size="3" face="Arial">
                                            No; aquí solo se <strong>autoriza</strong> la anulación. El proceso de
                                            anular se realiza desde "Registros de pago" una vez marcado como anulable.
                                        </font>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <!-- Pregunta 2 -->
                            <table border="1" cellpadding="10" cellspacing="0" width="100%"
                                bgcolor="#e7f3ff">
                                <tr>
                                    <td>
                                        <font size="3" color="#007bff" face="Arial">
                                            <strong>¿Qué significa "Pendiente Aprobación"?</strong>
                                        </font>
                                        <br><br>
                                        <font size="3" face="Arial">
                                            El pago fue anulado pero falta la aprobación final de un usuario con rol
                                            superior.
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

    <br><br>

    <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
        <tr>
            <td align="center">
                <font size="2" color="#ffffff" face="Arial">
                    Guía de la Gestión de Anulación de Pagos - Control Administrativo de Recibos
                </font>
            </td>
        </tr>
    </table>

</div>
