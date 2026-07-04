<div bgcolor="#ffffff">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#007bff" align="center" height="80">
                <font size="6" color="#ffffff" face="Arial">
                    <strong>Guía Completa - Histórico de Pagos</strong>
                </font>
            </td>
        </tr>
        <tr>
            <td bgcolor="#e9ecef" align="center" height="40">
                <font size="4" color="#495057" face="Arial">
                    Consulta y gestión del historial completo de transacciones financieras
                </font>
            </td>
        </tr>
    </table>

    <br>

    <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
        <tr>
            <td>
                <font size="3" color="#0c5460" face="Arial">
                    <strong>Característica:</strong> Sistema multi-moneda con soporte para Bolívares (Bs) y Dólares (USD), modal AJAX para detalles y generación de recibos PDF.
                </font>
            </td>
        </tr>
    </table>

    <br>

    <!-- INTRODUCCIÓN -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#dc3545" face="Arial">
                    <strong>1. Introducción</strong>
                </font>
                <br><br>
                <font size="3" face="Arial">
                    El <strong>Módulo de Histórico de Pagos</strong> es una herramienta integral diseñada para consultar, analizar y gestionar el historial completo de transacciones financieras de los representantes en la institución.
                </font>
                
                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#17a2b8" face="Arial">
                                <strong>Funcionalidades Principales:</strong>
                            </font>
                            <br>
                            <ul>
                                <li><strong>Búsqueda inteligente</strong> de representantes por CI o nombre</li>
                                <li><strong>Vista detallada</strong> de transacciones combinadas</li>
                                <li><strong>Análisis de recursos</strong> aplicados (efectivo, abonos, créditos)</li>
                                <li><strong>Generación de recibos</strong> históricos en PDF</li>
                                <li><strong>Detección automática</strong> de estados irregulares</li>
                                <li><strong>Acceso rápido</strong> al asistente de nuevos pagos</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- FLUJO PRINCIPAL -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#28a745" face="Arial">
                    <strong>2. Flujo Principal - Consulta en 4 Pasos</strong>
                </font>
                
                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="25%" bgcolor="#e7f3ff" align="center">
                            <font size="4" color="#007bff" face="Arial"><strong>1</strong></font>
                            <br>
                            <font size="3" face="Arial"><strong>Búsqueda</strong></font>
                            <br>
                            <font size="2" face="Arial">Filtrar representante por CI o nombre</font>
                            <br>
                            <font size="1" color="#6c757d" face="Arial">30 segundos</font>
                        </td>
                        <td width="25%" bgcolor="#e7f3ff" align="center">
                            <font size="4" color="#007bff" face="Arial"><strong>2</strong></font>
                            <br>
                            <font size="3" face="Arial"><strong>Vista General</strong></font>
                            <br>
                            <font size="2" face="Arial">Revisar tabla resumen de pagos</font>
                            <br>
                            <font size="1" color="#6c757d" face="Arial">1 minuto</font>
                        </td>
                        <td width="25%" bgcolor="#e7f3ff" align="center">
                            <font size="4" color="#007bff" face="Arial"><strong>3</strong></font>
                            <br>
                            <font size="3" face="Arial"><strong>Detalles</strong></font>
                            <br>
                            <font size="2" face="Arial">Modal con transacción específica</font>
                            <br>
                            <font size="1" color="#6c757d" face="Arial">2 minutos</font>
                        </td>
                        <td width="25%" bgcolor="#e7f3ff" align="center">
                            <font size="4" color="#007bff" face="Arial"><strong>4</strong></font>
                            <br>
                            <font size="3" face="Arial"><strong>Acciones</strong></font>
                            <br>
                            <font size="2" face="Arial">Generar PDF</font>
                            <br>
                            <font size="1" color="#6c757d" face="Arial">1 minuto</font>
                        </td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d4edda">
                    <tr>
                        <td align="center">
                            <font size="3" color="#155724" face="Arial">
                                <strong>Tiempo Total Estimado:</strong> Consultar el histórico completo toma aproximadamente <strong>4-5 minutos</strong> dependiendo de la cantidad de transacciones.
                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- PASO 1: BÚSQUEDA -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>3. Paso 1: Búsqueda Inteligente de Representante</strong>
                </font>
                
                <br><br>
                <font size="4" face="Arial"><strong>Métodos de Búsqueda Disponibles</strong></font>
                <br>
                <div class="table-responsive">
                    <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                        <tr bgcolor="#343a40">
                            <td width="25%"><font color="#ffffff" face="Arial"><strong>Campo</strong></font></td>
                            <td width="35%"><font color="#ffffff" face="Arial"><strong>Descripción</strong></font></td>
                            <td width="20%"><font color="#ffffff" face="Arial"><strong>Obligatorio</strong></font></td>
                            <td width="20%"><font color="#ffffff" face="Arial"><strong>Ejemplo</strong></font></td>
                        </tr>
                        <tr bgcolor="#f8f9fa">
                            <td><font face="Arial"><strong>Help Representante</strong></font></td>
                            <td><font face="Arial">Filtro en tiempo real por CI o nombre</font></td>
                            <td><font face="Arial"><span style="background-color:#ffc107; padding:2px 5px;">Opcional</span></font></td>
                            <td><font face="Arial">V-12345678</font></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td><font face="Arial"><strong>Representante ID</strong></font></td>
                            <td><font face="Arial">Selección desde lista desplegable</font></td>
                            <td><font face="Arial"><span style="background-color:#dc3545; color:white; padding:2px 5px;">Sí</span></font></td>
                            <td><font face="Arial">María González</font></td>
                        </tr>
                        <tr bgcolor="#f8f9fa">
                            <td><font face="Arial"><strong>Botón Buscar</strong></font></td>
                            <td><font face="Arial">Ejecuta consulta en base de datos</font></td>
                            <td><font face="Arial"><span style="background-color:#dc3545; color:white; padding:2px 5px;">Sí</span></font></td>
                            <td><font face="Arial">Acción principal</font></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                            <td><font face="Arial"><strong>Asistente Pago</strong></font></td>
                            <td><font face="Arial">Acceso rápido a registro de pagos</font></td>
                            <td><font face="Arial"><span style="background-color:#28a745; color:white; padding:2px 5px;">Navegación</span></font></td>
                            <td><font face="Arial">Botón verde con "+"</font></td>
                        </tr>
                    </table>
                </div>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#17a2b8" face="Arial">
                                <strong>Tip de Búsqueda:</strong> Use el campo "CI o nombre" para filtrar rápidamente la lista de representantes. Escriba la cédula o parte del nombre y la lista se actualizará automáticamente.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Características del Filtro Inteligente</strong></font>
                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#e7f3ff" valign="top">
                            <font size="3" color="#007bff" face="Arial">
                                <strong>Búsqueda Rápida (JavaScript)</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li>Filtra la lista desplegable en tiempo real</li>
                                <li>No requiere enviar el formulario</li>
                                <li>Mantiene la selección actual si existe</li>
                                <li>Funciona con expresiones regulares</li>
                                <li>Performance optimizado en cliente</li>
                                <li>Sin latencia de servidor</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#fff3cd" valign="top">
                            <font size="3" color="#856404" face="Arial">
                                <strong>Búsqueda Formal (Servidor)</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li>Lista completa desde base de datos</li>
                                <li>Datos validados y consistentes</li>
                                <li>Información actualizada en tiempo real</li>
                                <li>Requiere envío de formulario</li>
                                <li>Genera consulta SQL optimizada</li>
                                <li>Incluye relaciones con estudiantes</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- PASO 2: VISTA GENERAL -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>4. Paso 2: Vista General - Tabla de Histórico</strong>
                </font>
                
                <br><br>
                <font size="4" face="Arial"><strong>Estructura de la Tabla de Resultados</strong></font>
                <br>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="20%"><font color="#ffffff" face="Arial"><strong>Columna</strong></font></td>
                        <td width="30%"><font color="#ffffff" face="Arial"><strong>Descripción</strong></font></td>
                        <td width="25%"><font color="#ffffff" face="Arial"><strong>Formato</strong></font></td>
                        <td width="25%"><font color="#ffffff" face="Arial"><strong>Importancia</strong></font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>FEC. REGISTRO</strong></font></td>
                        <td><font face="Arial">Fecha y hora del pago combinado</font></td>
                        <td><font face="Arial">dd-mm-YYYY hh:ii</font></td>
                        <td><font face="Arial"><span style="background-color:#dc3545; color:white; padding:2px 5px;">Alta</span></font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>CONCEPTOS</strong></font></td>
                        <td><font face="Arial">Detalle de cuentas por pagar y estudiantes</font></td>
                        <td><font face="Arial">Lista con nombres</font></td>
                        <td><font face="Arial"><span style="background-color:#dc3545; color:white; padding:2px 5px;">Alta</span></font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>RECURSO</strong></font></td>
                        <td><font face="Arial">Total de recursos aplicados</font></td>
                        <td><font face="Arial">Monto Bs | $ USD</font></td>
                        <td><font face="Arial"><span style="background-color:#ffc107; padding:2px 5px;">Media</span></font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>PAGADO</strong></font></td>
                        <td><font face="Arial">Total aplicado a conceptos</font></td>
                        <td><font face="Arial">Monto Bs | $ USD</font></td>
                        <td><font face="Arial"><span style="background-color:#ffc107; padding:2px 5px;">Media</span></font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>CAF</strong></font></td>
                        <td><font face="Arial">Créditos a favor generados</font></td>
                        <td><font face="Arial">Monto Bs | $ USD</font></td>
                        <td><font face="Arial"><span style="background-color:#17a2b8; color:white; padding:2px 5px;">Baja</span></font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>ACCIÓN</strong></font></td>
                        <td><font face="Arial">Operaciones disponibles</font></td>
                        <td><font face="Arial">Grupo de botones</font></td>
                        <td><font face="Arial"><span style="background-color:#28a745; color:white; padding:2px 5px;">Funcional</span></font></td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Indicadores Visuales en la Tabla</strong></font>
                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#ffffff" valign="top">
                            <font size="3" color="#000000" face="Arial">
                                <strong>Fila Normal</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li>Fondo blanco estándar</li>
                                <li>Transacción regular</li>
                                <li>Sin problemas detectados</li>
                                <li>Todos los botones disponibles</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#f8d7da" valign="top">
                            <font size="3" color="#721c24" face="Arial">
                                <strong>Estado Irregular</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li>Fondo rojo claro (#f8d7da)</li>
                                <li>Problema en la transacción</li>
                                <li>Requiere revisión administrativa</li>
                                <li>Botón PDF deshabilitado</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Desglose de Recursos</strong></font>
                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                    <tr>
                        <td>
                            <font size="3" color="#0c5460" face="Arial">
                                <strong>Fórmula de Recursos:</strong> RECURSO = Σ(INGRESOS) + Σ(ABONOS) + Σ(CRÉDITOS APLICADOS)
                            </font>
                            <br><br>
                            <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td width="33%" bgcolor="#d4edda" align="center">
                                        <font size="3" color="#155724" face="Arial"><strong>INGRESOS (ING)</strong></font>
                                        <br>
                                        <font size="2" face="Arial">Pagos en efectivo, transferencias, depósitos</font>
                                    </td>
                                    <td width="34%" bgcolor="#fff3cd" align="center">
                                        <font size="3" color="#856404" face="Arial"><strong>ABONOS (ABN)</strong></font>
                                        <br>
                                        <font size="2" face="Arial">Pagos anticipados, recursos en tránsito</font>
                                    </td>
                                    <td width="33%" bgcolor="#e7f3ff" align="center">
                                        <font size="3" color="#007bff" face="Arial"><strong>CRÉDITOS (CAF)</strong></font>
                                        <br>
                                        <font size="2" face="Arial">Saldo a favor, excedentes, bonificaciones</font>
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

    <!-- PASO 3: MODAL DETALLES -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>5. Paso 3: Vista Detallada - Modal AJAX</strong>
                </font>
                
                <br><br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#17a2b8" face="Arial">
                                <strong>Tecnología AJAX:</strong> Los detalles se cargan mediante peticiones asíncronas sin recargar la página, mejorando la experiencia de usuario y el rendimiento del sistema.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Secciones del Modal de Detalles</strong></font>
                <br>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="25%"><font color="#ffffff" face="Arial"><strong>Sección</strong></font></td>
                        <td width="40%"><font color="#ffffff" face="Arial"><strong>Contenido</strong></font></td>
                        <td width="35%"><font color="#ffffff" face="Arial"><strong>Componentes Incluidos</strong></font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>REGISTRO</strong></font></td>
                        <td><font face="Arial">Información básica del pago combinado</font></td>
                        <td><font face="Arial">Fecha, ID, observaciones, usuario</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>TRANSACCIÓN</strong></font></td>
                        <td><font face="Arial">Detalle de ingresos y métodos de pago</font></td>
                        <td><font face="Arial">Bancos, referencias, montos, fechas</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td><font face="Arial"><strong>PAGADO</strong></font></td>
                        <td><font face="Arial">Desglose completo de aplicación</font></td>
                        <td><font face="Arial">Pagos, créditos, descuentos, abonos</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td><font face="Arial"><strong>CTA. CANCELADA</strong></font></td>
                        <td><font face="Arial">Conceptos específicos pagados</font></td>
                        <td><font face="Arial">Cuentas por pagar satisfechas</font></td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Ventajas del Sistema AJAX</strong></font>
                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="33%" bgcolor="#d4edda" align="center">
                            <font size="3" color="#155724" face="Arial"><strong>Carga Bajo Demanda</strong></font>
                            <br>
                            <font size="2" face="Arial">Solo cuando se necesita</font>
                        </td>
                        <td width="34%" bgcolor="#d4edda" align="center">
                            <font size="3" color="#155724" face="Arial"><strong>Sin Recarga</strong></font>
                            <br>
                            <font size="2" face="Arial">Mantiene contexto actual</font>
                        </td>
                        <td width="33%" bgcolor="#d4edda" align="center">
                            <font size="3" color="#155724" face="Arial"><strong>Rápido y Eficiente</strong></font>
                            <br>
                            <font size="2" face="Arial">Menos carga del servidor</font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- PASO 4: ACCIONES -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#007bff" face="Arial">
                    <strong>6. Paso 4: Acciones y Funcionalidades</strong>
                </font>
                
                <br><br>
                <font size="4" face="Arial"><strong>Botones de Acción por Registro</strong></font>
                <br>
                <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                    <tr bgcolor="#343a40">
                        <td width="20%"><font color="#ffffff" face="Arial"><strong>Botón</strong></font></td>
                        <td width="35%"><font color="#ffffff" face="Arial"><strong>Función</strong></font></td>
                        <td width="25%"><font color="#ffffff" face="Arial"><strong>Color/Estado</strong></font></td>
                        <td width="20%"><font color="#ffffff" face="Arial"><strong>Comportamiento</strong></font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial">
                                <button style="background-color:#17a2b8; color:white; border:none; padding:2px 8px;">ⓘ</button>
                                <strong> Detalles</strong>
                            </font>
                        </td>
                        <td><font face="Arial">Abre modal AJAX con información completa</font></td>
                        <td><font face="Arial"><span style="background-color:#17a2b8; color:white; padding:2px 5px;">Azul - Siempre activo</span></font></td>
                        <td><font face="Arial">Trigger AJAX → Carga modal</font></td>
                    </tr>
                    <tr bgcolor="#ffffff">
                        <td>
                            <font face="Arial">
                                <button style="background-color:#343a40; color:white; border:none; padding:2px 8px;">📄</button>
                                <strong> PDF</strong>
                            </font>
                        </td>
                        <td><font face="Arial">Genera recibo oficial en PDF</font></td>
                        <td><font face="Arial"><span style="background-color:#343a40; color:white; padding:2px 5px;">Negro - Condicional</span></font></td>
                        <td><font face="Arial">Deshabilitado si estado irregular</font></td>
                    </tr>
                    <tr bgcolor="#f8f9fa">
                        <td>
                            <font face="Arial">
                                <button style="background-color:#28a745; color:white; border:none; padding:2px 8px;">+</button>
                                <strong> Asistente</strong>
                            </font>
                        </td>
                        <td><font face="Arial">Acceso rápido a registro de pagos</font></td>
                        <td><font face="Arial"><span style="background-color:#28a745; color:white; padding:2px 5px;">Verde - Global</span></font></td>
                        <td><font face="Arial">Navegación directa</font></td>
                    </tr>
                </table>

                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="3" color="#856404" face="Arial">
                                <strong>Importante:</strong> Cuando un pago tiene <strong>status_irregular = true</strong>, el botón PDF se deshabilita automáticamente para evitar generar comprobantes de transacciones problemáticas.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>
                <font size="4" face="Arial"><strong>Generación de Recibos PDF</strong></font>
                <br>
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="3" color="#007bff" face="Arial">
                                <strong>Características del Recibo PDF:</strong>
                            </font>
                            <br><br>
                            <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td width="50%" bgcolor="#f8f9fa" valign="top">
                                        <font size="3" face="Arial"><strong>Información Institucional</strong></font>
                                        <ul>
                                            <li>Logo oficial y datos de la institución</li>
                                            <li>Dirección, teléfonos, RIF</li>
                                            <li>Encabezado profesional estandarizado</li>
                                            <li>Número de recibo único y correlativo</li>
                                        </ul>
                                    </td>
                                    <td width="50%" bgcolor="#f8f9fa" valign="top">
                                        <font size="3" face="Arial"><strong>Contenido del Recibo</strong></font>
                                        <ul>
                                            <li>Datos completos del representante</li>
                                            <li>Fecha y hora de emisión exacta</li>
                                            <li>Detalle de transacción específica</li>
                                            <li>Montos en Bs y USD diferenciados</li>
                                            <li>Firma digital del sistema</li>
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

    <br>

    <!-- PREGUNTAS FRECUENTES -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#ffc107" face="Arial">
                    <strong>7. Preguntas Frecuentes (FAQ)</strong>
                </font>
                
                <br><br>
                
                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#e7f3ff">
                    <tr>
                        <td>
                            <font size="4" color="#007bff" face="Arial">
                                <strong>¿Por qué algunos pagos aparecen en rojo en la tabla?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Los pagos con <strong style="color:#dc3545;">fondo rojo</strong> tienen <strong>status_irregular = true</strong>. Esto indica problemas que requieren revisión:
                            </font>
                            <ul>
                                <li>Discrepancia en montos reportados vs aplicados</li>
                                <li>Problemas de conciliación bancaria</li>
                                <li>Errores en la distribución de recursos</li>
                                <li>Transacciones marcadas para revisión administrativa</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#fff3cd">
                    <tr>
                        <td>
                            <font size="4" color="#856404" face="Arial">
                                <strong>¿Puedo generar un recibo PDF de un pago con estado irregular?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                <strong style="color:#dc3545;">No, no es posible.</strong> El sistema deshabilita automáticamente el botón de PDF para pagos con estado irregular. Esto evita generar comprobantes oficiales de transacciones que requieren revisión o validación administrativa previa.
                            </font>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#d1ecf1">
                    <tr>
                        <td>
                            <font size="4" color="#0c5460" face="Arial">
                                <strong>¿Qué significa la diferencia entre "RECURSO" y "PAGADO"?</strong>
                            </font>
                            <br><br>
                            <table border="1" cellpadding="8" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr>
                                    <td width="50%" bgcolor="#d4edda" valign="top">
                                        <font size="3" color="#155724" face="Arial"><strong>RECURSO</strong></font>
                                        <br><br>
                                        <ul>
                                            <li>Total de fondos disponibles</li>
                                            <li>Suma de todos los ingresos</li>
                                            <li>Incluye abonos aplicados</li>
                                            <li>Incluye créditos utilizados</li>
                                            <li><strong>FÓRMULA:</strong> ING + ABN + CAF</li>
                                        </ul>
                                    </td>
                                    <td width="50%" bgcolor="#e7f3ff" valign="top">
                                        <font size="3" color="#007bff" face="Arial"><strong>PAGADO</strong></font>
                                        <br><br>
                                        <ul>
                                            <li>Total aplicado a conceptos</li>
                                            <li>Montos asignados a cuotas</li>
                                            <li>Pagos efectivamente realizados</li>
                                            <li>Recursos utilizados</li>
                                            <li><strong>RESULTADO:</strong> RECURSO - CAF_GENERADO</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="1" cellpadding="10" cellspacing="0" width="100%" bgcolor="#ffe6e6">
                    <tr>
                        <td>
                            <font size="4" color="#dc3545" face="Arial">
                                <strong>¿Por qué el botón de PDF no se habilita?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial"><strong>Causas comunes:</strong></font>
                            <br><br>
                            <table border="1" cellpadding="6" cellspacing="0" width="100%" bgcolor="#ffffff">
                                <tr bgcolor="#343a40">
                                    <td width="40%"><font color="#ffffff" face="Arial"><strong>Causa</strong></font></td>
                                    <td width="35%"><font color="#ffffff" face="Arial"><strong>Solución</strong></font></td>
                                    <td width="25%"><font color="#ffffff" face="Arial"><strong>Indicador</strong></font></td>
                                </tr>
                                <tr bgcolor="#f8f9fa">
                                    <td><font face="Arial"><strong>Estado irregular</strong></font></td>
                                    <td><font face="Arial">Contacte al administrador</font></td>
                                    <td><font color="#dc3545" face="Arial">Fila roja</font></td>
                                </tr>
                                <tr bgcolor="#ffffff">
                                    <td><font face="Arial"><strong>Problema técnico</strong></font></td>
                                    <td><font face="Arial">Recargue la página</font></td>
                                    <td><font color="#6c757d" face="Arial">Botón gris</font></td>
                                </tr>
                                <tr bgcolor="#f8f9fa">
                                    <td><font face="Arial"><strong>Sin permisos</strong></font></td>
                                    <td><font face="Arial">Verifique su rol de usuario</font></td>
                                    <td><font color="#6c757d" face="Arial">Botón gris</font></td>
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
                                <strong>¿Cómo accedo rápidamente a registrar un nuevo pago?</strong>
                            </font>
                            <br><br>
                            <font size="3" face="Arial">
                                Use el <strong style="color:#28a745;">botón verde con icono "+"</strong> ubicado en la barra de búsqueda. Esta funcionalidad ofrece:
                            </font>
                            <ul>
                                <li>Acceso directo al asistente de registro de pagos</li>
                                <li>Representante actual pre-seleccionado automáticamente</li>
                                <li>Ahorro de tiempo al evitar nueva búsqueda</li>
                                <li>Flujo continuo entre consulta y registro</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- RESUMEN -->
    <table border="1" cellpadding="15" cellspacing="0" width="100%" bgcolor="#f8f9fa">
        <tr>
            <td>
                <font size="5" color="#28a745" face="Arial">
                    <strong>8. Resumen de Características</strong>
                </font>
                
                <br><br>
                
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" bgcolor="#d4edda" valign="top">
                            <font size="4" color="#155724" face="Arial">
                                <strong>Funcionalidades Principales</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>Búsqueda inteligente</strong> por CI o nombre</li>
                                <li><strong>Sistema multi-moneda</strong> (Bs y USD)</li>
                                <li><strong>Modal AJAX</strong> para detalles rápidos</li>
                                <li><strong>Generación de PDF</strong> de recibos</li>
                                <li><strong>Detección automática</strong> de irregularidades</li>
                                <li><strong>Acceso rápido</strong> a nuevo pago</li>
                            </ul>
                        </td>
                        <td width="50%" bgcolor="#d1ecf1" valign="top">
                            <font size="4" color="#0c5460" face="Arial">
                                <strong>Ventajas del Sistema</strong>
                            </font>
                            <br><br>
                            <ul>
                                <li><strong>Interfaz responsive</strong> para todos los dispositivos</li>
                                <li><strong>Rendimiento optimizado</strong> con AJAX</li>
                                <li><strong>Seguridad</strong> con validación de estados</li>
                                <li><strong>Auditoría completa</strong> de transacciones</li>
                                <li><strong>Control preventivo</strong> de irregularidades</li>
                                <li><strong>Integración fluida</strong> con otros módulos</li>
                            </ul>
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
                            <font size="3" face="Arial">Contacte al administrador del sistema o consulte la documentación técnica para información adicional.</font>
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
                    Guía del Sistema de Histórico de Pagos - Versión 1.0 - Sistema Multi-Moneda con Validación AJAX
                </font>
            </td>
        </tr>
    </table>

</div>