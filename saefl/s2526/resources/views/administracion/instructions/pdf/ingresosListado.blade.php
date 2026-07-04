<div bgcolor="#ffffff">

    <!-- HEADER PRINCIPAL -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#007bff" align="center" height="80">
                <font size="6" color="#ffffff" face="Arial">
                    <strong>Guía Completa - Listado de Ingresos Registrados</strong>
                </font>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e9ecef" align="center" height="40">
                <font size="4" color="#495057" face="Arial">
                    Sistema avanzado para consulta, filtrado y gestión de ingresos institucionales
                </font>
            </td>
        </tr>
    </table>

    <br>

    <!-- SECCIÓN INTRODUCCIÓN -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>1. Introducción</strong>
                </font>
                <br><br>
                <font size="3" face="Arial">
                    El <strong>Listado de Ingresos Registrados</strong> es una herramienta para consultar, filtrar y administrar todos los ingresos reportados en la institución con capacidades de búsqueda avanzada y gestión edición/actualización completa.
                </font>
                
                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#17a2b8" face="Arial">
                                <strong>Propósito del Sistema:</strong>
                            </font>
                            <br>
                            <ul>
                                <li>Consulta centralizada de todos los ingresos</li>
                                <li>Filtrado avanzado por múltiples criterios</li>
                                <li>Gestión completa edición/actualización de registros</li>
                                <li>Reportes financieros en tiempo real</li>
                                <li>Exportación de datos para análisis</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d4edda">
                    <tr>
                        <td align="center">
                            <font size="3" color="#155724" face="Arial">
                                <strong>Características Principales</strong>
                            </font>
                            <br>
                            <table border="0" cellpadding="5" cellspacing="5" width="100%">
                                <tr>
                                    <td align="center" width="20%">
                                        <font face="Arial">Búsqueda Avanzada</font>
                                    </td>
                                    <td align="center" width="20%">
                                        <font face="Arial">Edición/Actualización</font>
                                    </td>
                                    <td align="center" width="20%">
                                        <font face="Arial">Totales Automáticos</font>
                                    </td>
                                    <td align="center" width="20%">
                                        <font face="Arial">Exportación PDF</font>
                                    </td>
                                    <td align="center" width="20%">
                                        <font face="Arial">Auditoría Completa</font>
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

    <!-- ESTRUCTURA DE DATOS -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#28a745" face="Arial">
                    <strong>2. Estructura de Datos</strong>
                </font>
                
                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e9ecef">
                    <tr>
                        <td>
                            <font size="3" color="#495057" face="Arial">
                                <strong>Relaciones Principales:</strong> Cada ingreso está relacionado con múltiples entidades del sistema para garantizar integridad referencial.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#d4edda" valign="top">
                            <font size="4" color="#155724" face="Arial">
                                <strong>Relaciones del Modelo Ingreso</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>belongsTo Estudiante</strong> - Estudiante asociado</li>
                                <li><strong>belongsTo Representante</strong> - Persona que paga</li>
                                <li><strong>belongsTo Banco</strong> - Entidad bancaria</li>
                                <li><strong>belongsTo MetodoPago</strong> - Forma de pago</li>
                                <li><strong>belongsTo ExchangeRate</strong> - Tasa de cambio</li>
                                <li><strong>belongsTo User</strong> - Usuario que registra</li>
                                <li><strong>SoftDeletes</strong> - Eliminación lógica</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#cce7ff" valign="top">
                            <font size="4" color="#004085" face="Arial">
                                <strong>Campos Principales</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>number_i_pay</strong> - Referencia única</li>
                                <li><strong>date_payment</strong> - Fecha de pago</li>
                                <li><strong>date_transaction</strong> - Fecha en banco</li>
                                <li><strong>ingreso_ammount</strong> - Monto principal</li>
                                <li><strong>exchange_ammount</strong> - Monto cambiario</li>
                                <li><strong>status_late_payment</strong> - Estado extemporáneo</li>
                                <li><strong>ingreso_observations</strong> - Observaciones</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- FILTRADO AVANZADO -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>3. Filtrado Avanzado</strong>
                </font>
                
                <br><br>
                <font size="4" face="Arial"><strong>Criterios de Búsqueda Disponibles</strong></font>
                <br><br>
                
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="25%"><font color="#ffffff" face="Arial"><strong>Filtro</strong></font></td>
                        <td width="45%"><font color="#ffffff" face="Arial"><strong>Descripción</strong></font></td>
                        <td width="15%"><font color="#ffffff" face="Arial"><strong>Tipo</strong></font></td>
                        <td width="15%"><font color="#ffffff" face="Arial"><strong>Ejemplo</strong></font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Fechas de Pago</strong></font></td>
                        <td><font face="Arial">Rango entre fecha inicial y final</font></td>
                        <td><font face="Arial">Fecha</font></td>
                        <td><font face="Arial">01/10/2024 - 31/10/2024</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Banco Receptor</strong></font></td>
                        <td><font face="Arial">Entidad bancaria específica</font></td>
                        <td><font face="Arial">Select</font></td>
                        <td><font face="Arial">Banco de Venezuela</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Identificación</strong></font></td>
                        <td><font face="Arial">Cédula estudiante o representante</font></td>
                        <td><font face="Arial">Texto</font></td>
                        <td><font face="Arial">12345678</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Referencia</strong></font></td>
                        <td><font face="Arial">Número de transacción bancaria</font></td>
                        <td><font face="Arial">Texto</font></td>
                        <td><font face="Arial">TRF-123456789</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Extemporáneos</strong></font></td>
                        <td><font face="Arial">Pagos fuera de fecha límite</font></td>
                        <td><font face="Arial">Switch</font></td>
                        <td><font face="Arial">Activado/Desactivado</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Banco Público</strong></font></td>
                        <td><font face="Arial">Filtrar solo bancos públicos</font></td>
                        <td><font face="Arial">Switch</font></td>
                        <td><font face="Arial">Activado/Desactivado</font></td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#17a2b8" face="Arial">
                                <strong>Tips de Búsqueda:</strong>
                            </font>
                            <ul>
                                <li>Use % para búsquedas parciales</li>
                                <li>Fechas vacías = sin filtro temporal</li>
                                <li>Múltiples filtros se combinan con AND</li>
                                <li>Los switches son excluyentes</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="3" color="#856404" face="Arial">
                                <strong>Performance:</strong> Las búsquedas con muchos filtros pueden tomar más tiempo. Use solo los necesarios.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Ejemplos de Uso de Filtros</strong></font>
                <br><br>
                
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#e7f3ff" valign="top">
                            <font size="4" color="#007bff" face="Arial">
                                <strong>Búsqueda por Período</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Objetivo:</strong> Ver todos los ingresos de octubre 2024</font>
                            <ul>
                                <li><strong>Fecha Inicial:</strong> 2024-10-01</li>
                                <li><strong>Fecha Final:</strong> 2024-10-31</li>
                                <li><strong>Resultado:</strong> Ingresos del mes completo</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#ffe6e6" valign="top">
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>Búsqueda por Persona</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Objetivo:</strong> Encontrar pagos de un representante</font>
                            <ul>
                                <li><strong>Identificación:</strong> V-12345678</li>
                                <li><strong>Resultado:</strong> Todos los pagos de esa cédula</li>
                                <li><strong>Nota:</strong> Busca en estudiante y representante</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- ESTRUCTURA DE LA TABLA -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>4. Estructura de la Tabla Principal</strong>
                </font>
                
                <br><br>
                <font size="4" face="Arial"><strong>Columnas y Responsividad</strong></font>
                <br><br>
                
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="25%"><font color="#ffffff" face="Arial"><strong>Columna</strong></font></td>
                        <td width="35%"><font color="#ffffff" face="Arial"><strong>Descripción</strong></font></td>
                        <td width="20%"><font color="#ffffff" face="Arial"><strong>Visible en</strong></font></td>
                        <td width="20%"><font color="#ffffff" face="Arial"><strong>Contenido</strong></font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>N</strong></font></td>
                        <td><font face="Arial">Número de orden</font></td>
                        <td><font face="Arial">Todos</font></td>
                        <td><font face="Arial">Iteración del loop</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>ID</strong></font></td>
                        <td><font face="Arial">Identificador único</font></td>
                        <td><font face="Arial">Todos</font></td>
                        <td><font face="Arial">ID del registro</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Representante</strong></font></td>
                        <td><font face="Arial">Persona que realizó el pago</font></td>
                        <td><font face="Arial">Siempre</font></td>
                        <td><font face="Arial">Nombre + Cédula</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>F. Pago</strong></font></td>
                        <td><font face="Arial">Fecha de realización del pago</font></td>
                        <td><font face="Arial">Tablet+</font></td>
                        <td><font face="Arial">dd-mm-aaaa</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>F. Banco</strong></font></td>
                        <td><font face="Arial">Fecha de transacción bancaria</font></td>
                        <td><font face="Arial">Tablet+</font></td>
                        <td><font face="Arial">dd-mm-aaaa</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Banco</strong></font></td>
                        <td><font face="Arial">Entidad receptora</font></td>
                        <td><font face="Arial">Tablet+</font></td>
                        <td><font face="Arial">Nombre del banco</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Referencia</strong></font></td>
                        <td><font face="Arial">Número de transacción</font></td>
                        <td><font face="Arial">Desktop+</font></td>
                        <td><font face="Arial">Número único</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Monto (Bs)</strong></font></td>
                        <td><font face="Arial">Monto en moneda local</font></td>
                        <td><font face="Arial">Desktop+</font></td>
                        <td><font face="Arial">Formato decimal</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>M. Cambiario ($)</strong></font></td>
                        <td><font face="Arial">Monto en dólares</font></td>
                        <td><font face="Arial">Desktop+</font></td>
                        <td><font face="Arial">Formato decimal</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Destino</strong></font></td>
                        <td><font face="Arial">Concepto del pago</font></td>
                        <td><font face="Arial">Desktop+</font></td>
                        <td><font face="Arial">Descripción</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>F. Registro</strong></font></td>
                        <td><font face="Arial">Fecha de creación</font></td>
                        <td><font face="Arial">Tablet+</font></td>
                        <td><font face="Arial">dd-mm-aaaa</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Usuario</strong></font></td>
                        <td><font face="Arial">Registrador</font></td>
                        <td><font face="Arial">Tablet+</font></td>
                        <td><font face="Arial">Username</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Acción</strong></font></td>
                        <td><font face="Arial">Operaciones</font></td>
                        <td><font face="Arial">Siempre</font></td>
                        <td><font face="Arial">Botones Editar/Eliminar</font></td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Indicadores Visuales</strong></font>
                <br><br>
                
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#ffe6e6" valign="top">
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>Registros Eliminados</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">Los ingresos eliminados lógicamente se muestran con:</font>
                            <ul>
                                <li><strong>Fondo rojo</strong> en toda la fila</li>
                                <li><strong>Texto [BORRADO]</strong> en la referencia</li>
                                <li><strong>Botones deshabilitados</strong> para edición</li>
                                <li><strong>Solo visible</strong> para administradores</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#fff3cd" valign="top">
                            <font size="4" color="#856404" face="Arial">
                                <strong>Tasa de Cambio</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">Indicadores de conversión monetaria:</font>
                            <ul>
                                <li><strong>Texto en negrita</strong> cuando hay tasa</li>
                                <li><strong>Color azul</strong> para montos convertidos</li>
                                <li><strong>Tooltip</strong> con detalles de la tasa</li>
                                <li><strong>Texto normal</strong> sin tasa disponible</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- OPERACIONES DISPONIBLES -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>5. Operaciones Disponibles</strong>
                </font>
                
                <br><br>
                <font size="4" face="Arial"><strong>Edición de Ingresos</strong></font>
                <br><br>
                
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="25%"><font color="#ffffff" face="Arial"><strong>Campo Editable</strong></font></td>
                        <td width="35%"><font color="#ffffff" face="Arial"><strong>Descripción</strong></font></td>
                        <td width="20%"><font color="#ffffff" face="Arial"><strong>Validación</strong></font></td>
                        <td width="20%"><font color="#ffffff" face="Arial"><strong>Ejemplo</strong></font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Método de Pago</strong></font></td>
                        <td><font face="Arial">Forma de pago utilizada</font></td>
                        <td><font color="#dc3545" face="Arial">Requerido</font></td>
                        <td><font face="Arial">Transferencia, Efectivo</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Banco</strong></font></td>
                        <td><font face="Arial">Entidad bancaria</font></td>
                        <td><font color="#dc3545" face="Arial">Requerido</font></td>
                        <td><font face="Arial">Banco de Venezuela</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Referencia</strong></font></td>
                        <td><font face="Arial">Número de transacción</font></td>
                        <td><font color="#dc3545" face="Arial">Requerido</font></td>
                        <td><font face="Arial">TRF-123456789</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Monto del Pago</strong></font></td>
                        <td><font face="Arial">Monto principal</font></td>
                        <td><font color="#dc3545" face="Arial">Requerido</font></td>
                        <td><font face="Arial">120.00</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Monto Extemporáneo</strong></font></td>
                        <td><font face="Arial">Monto cambiario adicional</font></td>
                        <td><font color="#ffc107" face="Arial">Opcional</font></td>
                        <td><font face="Arial">5.00</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Fecha de Pago</strong></font></td>
                        <td><font face="Arial">Fecha del pago</font></td>
                        <td><font color="#dc3545" face="Arial">Requerido</font></td>
                        <td><font face="Arial">15/10/2024</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>Fecha en Banco</strong></font></td>
                        <td><font face="Arial">Fecha bancaria</font></td>
                        <td><font color="#dc3545" face="Arial">Requerido</font></td>
                        <td><font face="Arial">15/10/2024</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>Observaciones</strong></font></td>
                        <td><font face="Arial">Notas adicionales</font></td>
                        <td><font color="#ffc107" face="Arial">Opcional</font></td>
                        <td><font face="Arial">Pago parcial</font></td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#17a2b8" face="Arial">
                                <strong>Cálculo Automático:</strong>
                            </font>
                            <ul>
                                <li>Al guardar, busca tasa de cambio de la fecha</li>
                                <li>Recalcula monto cambiario automáticamente</li>
                                <li>Si no hay tasa, campos quedan nulos</li>
                                <li>Actualiza todos los relacionados</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="3" color="#856404" face="Arial">
                                <strong>Auditoría:</strong> La edición mantiene el usuario original pero actualiza timestamps.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Eliminación Lógica</strong></font>
                <br><br>
                
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffe6e6">
                    <tr>
                        <td>
                            <font size="3" color="#dc3545" face="Arial">
                                <strong>Proceso de Eliminación:</strong> La eliminación es <strong>LÓGICA (Soft Delete)</strong> - Los registros se marcan como eliminados pero permanecen en la base de datos para auditoría.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#f8f9fa" valign="top">
                            <font size="4" color="#495057" face="Arial">
                                <strong>Acciones al Eliminar</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>Marca deleted_at</strong> con timestamp</li>
                                <li><strong>Modifica number_i_pay</strong> añadiendo "[BORRADO]"</li>
                                <li><strong>Mantiene relaciones</strong> intactas</li>
                                <li><strong>Permite recuperación</strong> vía administración</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#f8f9fa" valign="top">
                            <font size="4" color="#495057" face="Arial">
                                <strong>Comportamiento Visual</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>Fila en rojo</strong> en listados</li>
                                <li><strong>Excluido</strong> de búsquedas normales</li>
                                <li><strong>Visible solo</strong> para administradores</li>
                                <li><strong>Botón editar</strong> deshabilitado</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- REPORTES Y EXPORTACIÓN -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>6. Reportes y Exportación de Datos</strong>
                </font>
                
                <br><br>
                <font size="4" face="Arial"><strong>Totales Automáticos</strong></font>
                <br><br>
                
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d4edda">
                    <tr>
                        <td>
                            <font size="3" color="#155724" face="Arial">
                                <strong>Resumen Financiero:</strong> El sistema calcula y muestra automáticamente los totales generales en ambas monedas al aplicar filtros.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#e7f3ff" valign="top" align="center">
                            <font size="4" color="#007bff" face="Arial">
                                <strong>Total General</strong>
                            </font>
                            <br><br>
                            <font size="5" face="Arial"><strong>Bs 1000.00</strong></font>
                            <br>
                            <font size="2" color="#6c757d" face="Arial">Sumatoria de todos los montos principales</font>
                        </td>
                        <td width="50%" bgcolor="#cce7ff" valign="top" align="center">
                            <font size="4" color="#004085" face="Arial">
                                <strong>Total Monto Cambiario</strong>
                            </font>
                            <br><br>
                            <font size="5" face="Arial"><strong>USD 100.00</strong></font>
                            <br>
                            <font size="2" color="#6c757d" face="Arial">Sumatoria de montos en moneda de referencia</font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Exportación PDF</strong></font>
                <br><br>
                
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="70%" bgcolor="#f8f9fa" valign="top">
                            <font size="4" color="#495057" face="Arial">
                                <strong>Generación de Reportes PDF</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Características del PDF generado:</strong></font>
                            <ul>
                                <li><strong>Incluye todos los filtros aplicados</strong></li>
                                <li><strong>Mantiene el formato de la tabla</strong></li>
                                <li><strong>Incluye totales y resúmenes</strong></li>
                                <li><strong>Encabezado institucional</strong></li>
                                <li><strong>Fecha y hora de generación</strong></li>
                                <li><strong>Paginación automática</strong></li>
                            </ul>
                        </td>
                        <td width="30%" bgcolor="#fff3cd" valign="top">
                            <font size="4" color="#856404" face="Arial">
                                <strong>Cómo Generar PDF</strong>
                            </font>
                            <br><br>
                            <ol>
                                <li>Aplicar filtros deseados</li>
                                <li>Hacer click en 
                                    <font color="#343a40" face="Arial">■</font>
                                </li>
                                <li>Se abre en nueva pestaña</li>
                                <li>Imprimir o guardar</li>
                            </ol>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- PERMISOS Y SEGURIDAD -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>7. Permisos y Seguridad</strong>
                </font>
                
                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="3" color="#856404" face="Arial">
                                <strong>Restricciones por Rol:</strong> El acceso al listado de ingresos está restringido a usuarios con rol de administrador mediante middleware.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#d4edda" valign="top">
                            <font size="4" color="#155724" face="Arial">
                                <strong>Operaciones Permitidas</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>Ver listado completo</strong></li>
                                <li><strong>Aplicar filtros de búsqueda</strong></li>
                                <li><strong>Exportar a PDF</strong></li>
                                <li><strong>Ver detalles</strong></li>
                                <li><strong>Navegar entre páginas</strong></li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#ffe6e6" valign="top">
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>Operaciones Restringidas</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>Editar registros</strong> (solo admin)</li>
                                <li><strong>Eliminar registros</strong> (solo admin)</li>
                                <li><strong>Ver eliminados</strong> (solo admin)</li>
                                <li><strong>Acceso sin autenticación</strong></li>
                            </ul>
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
                <font size="5" color="#007bff" face="Arial">
                    <strong>8. Preguntas Frecuentes (FAQ)</strong>
                </font>
                
                <br><br>
                
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="4" color="#007bff" face="Arial">
                                <strong>¿Cómo busco pagos de un representante específico?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Use el campo <strong>"Identificador"</strong> e ingrese la cédula del representante (con o sin formato). El sistema buscará en estudiante y representante automáticamente.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="4" color="#856404" face="Arial">
                                <strong>¿Por qué algunos montos aparecen en negrita y azul?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Indican que tienen una <strong>tasa de cambio asociada</strong>. Pase el mouse sobre el monto para ver los detalles de la tasa de cambio utilizada.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffe6e6">
                    <tr>
                        <td>
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>¿Qué significa el fondo rojo en algunas filas?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Indica que el registro fue <strong>eliminado lógicamente</strong>. Solo los administradores pueden ver estos registros y no son editables.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="4" color="#007bff" face="Arial">
                                <strong>¿Cómo exporto los datos filtrados a PDF?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Aplique los filtros deseados y haga click en el botón 
                                <font color="#343a40" face="Arial">■</font> 
                                en el formulario de búsqueda. Se generará un PDF con los datos actuales.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="4" color="#856404" face="Arial">
                                <strong>¿Puedo editar un pago registrado por error?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                <strong>Sí</strong>, si tiene permisos de administrador. Use el botón 
                                <font color="#ffc107" face="Arial">■</font> 
                                para acceder al formulario de edición. Los cambios recalculan automáticamente los montos cambiarios.
                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

    <!-- FOOTER -->
    <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
        <tr>
            <td align="center">
                <font size="2" color="#ffffff" face="Arial">
                    Guía del Sistema de Listado de Ingresos Registrados - Versión 1.0
                </font>
            </td>
        </tr>
    </table>

</div>