<div>
    <center>
        <h1>Guía de Consulta de Deudas por Mensualidad</h1>
        <p><i>Documentación Completa - Versión Imprimible</i></p>
        <hr size="2" color="black">
    </center>

    <!-- Introducción -->
    <h2>Consulta de Deudas por Mensualidad</h2>
    
    <p><b>Consulta de Deudas por Mensualidad es una herramienta especializada para identificar y gestionar las obligaciones pendientes de los representantes organizadas por periodos mensuales específicos.</b></p>
    
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr bgcolor="#f0f0f0">
            <th width="50%"><b>Propósito del Listado</b></th>
            <th width="50%"><b>Características Principales</b></th>
        </tr>
        <tr>
            <td valign="top">
                <ul>
                    <li>Consulta centralizada de deudas por periodo mensual</li>
                    <li>Filtrado inteligente por plan de pago y grado</li>
                    <li>Cálculo automatizado de saldos y montos pendientes</li>
                    <li>Exportación de reportes para seguimiento</li>
                    <li>Integración con el sistema de pagos principal</li>
                </ul>
            </td>
            <td valign="top">
                <ul>
                    <li><b>Filtro Mensual:</b> Consulta por periodos específicos</li>
                    <li><b>Múltiples Planes:</b> Compatibilidad con diferentes planes</li>
                    <li><b>Por Grados:</b> Filtrado por nivel educativo</li>
                    <li><b>Exportación:</b> Reportes en Excel, CSV y PDF</li>
                    <li><b>Deudas Detalladas:</b> Desglose completo</li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Requisitos del Sistema -->
    <h2>Requisitos</h2>
    
    <table border="1" cellpadding="4" cellspacing="0" width="100%" bgcolor="#fff3cd">
        <tr>
            <td><b>⚠ Requisito Crítico:</b> Deben existir Conceptos por cobrar generadas en el SAEFL. El reporte no mostrará datos sin cuotas creadas para el periodo seleccionado.</td>
        </tr>
    </table>
    
    <br>
    
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td width="50%" valign="top">
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr bgcolor="#f0f0f0">
                        <th><b>Configuraciones Requeridas</b></th>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li>Conceptos por cobrar generadas para el periodo</li>
                                <li>Representantes registrados en el SAEFL</li>
                                <li>Estudiantes activos asociados a representantes</li>
                                <li>Planes de pago configurados</li>
                                <li>Grados académicos definidos</li>
                                <li>Inscripciones vigentes de estudiantes</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" valign="top">
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr bgcolor="#f0f0f0">
                        <th><b>Elementos Calculados</b></th>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li>Deudas pendientes por estudiante</li>
                                <li>Abonos disponibles del representante</li>
                                <li>Créditos a favor del representante</li>
                                <li>Saldo a favor calculado</li>
                                <li>Deuda total consolidada</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Flujo de Trabajo -->
    <h2>Flujo de Trabajo - 4 Pasos</h2>
    
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td width="25%" align="center">
                <table border="1" cellpadding="8" cellspacing="0" width="90%">
                    <tr>
                        <td align="center">
                            <div style="width: 30px; height: 30px; background: black; color: white; border-radius: 50%; margin: 0 auto; line-height: 30px;"><b>1</b></div>
                            <br>
                            <b>Selección de Periodo</b>
                            <br><small>Elegir mensualidad de referencia</small>
                            <br><br>
                            <small><b>30 segundos</b></small>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="25%" align="center">
                <table border="1" cellpadding="8" cellspacing="0" width="90%">
                    <tr>
                        <td align="center">
                            <div style="width: 30px; height: 30px; background: black; color: white; border-radius: 50%; margin: 0 auto; line-height: 30px;"><b>2</b></div>
                            <br>
                            <b>Aplicar Filtros</b>
                            <br><small>Plan de pago y grado</small>
                            <br><br>
                            <small><b>20 segundos</b></small>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="25%" align="center">
                <table border="1" cellpadding="8" cellspacing="0" width="90%">
                    <tr>
                        <td align="center">
                            <div style="width: 30px; height: 30px; background: black; color: white; border-radius: 50%; margin: 0 auto; line-height: 30px;"><b>3</b></div>
                            <br>
                            <b>Revisar Resultados</b>
                            <br><small>Analizar deudas detectadas</small>
                            <br><br>
                            <small><b>2 minutos</b></small>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="25%" align="center">
                <table border="1" cellpadding="8" cellspacing="0" width="90%">
                    <tr>
                        <td align="center">
                            <div style="width: 30px; height: 30px; background: black; color: white; border-radius: 50%; margin: 0 auto; line-height: 30px;"><b>4</b></div>
                            <br>
                            <b>Exportar Reporte</b>
                            <br><small>Generar archivo para seguimiento</small>
                            <br><br>
                            <small><b>1 minuto</b></small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <br>
    
    <table border="1" cellpadding="4" cellspacing="0" width="100%" bgcolor="#d1edff">
        <tr>
            <td><b>Tiempo Total Estimado:</b> Obtener un reporte completo toma aproximadamente <b>4-5 minutos</b> dependiendo de la cantidad de datos.</td>
        </tr>
    </table>

    <!-- Paso 1: Selección de Periodo -->
    <h2>Paso 1: Selección de Mensualidad</h2>
    
    <h3>Parámetro Principal</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr bgcolor="#f0f0f0">
            <th width="30%">Campo</th>
            <th width="40%">Descripción</th>
            <th width="15%">Obligatorio</th>
            <th width="15%">Ejemplo</th>
        </tr>
        <tr>
            <td><b>Mensualidad de Referencia</b></td>
            <td>Periodo mensual para consultar deudas</td>
            <td align="center"><b>SÍ</b></td>
            <td>OCTUBRE</td>
        </tr>
    </table>
    
    <h3>Cómo Funciona el Filtro</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%" bgcolor="#e3f2fd">
        <tr>
            <td>
                <b>Lógica de Filtrado:</b>
                <ul>
                    <li>Selecciona Conceptos por cobrar con date_expiration dentro del mes</li>
                    <li>Excluye automáticamente recargos por morosidad (quota_original_id IS NULL)</li>
                    <li>Agrupa por representante y calcula deudas consolidadas</li>
                    <li>Muestra solo representantes con deudas pendientes > 0</li>
                </ul>
            </td>
        </tr>
    </table>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr bgcolor="#f0f0f0">
            <th>Fuente de Datos</th>
        </tr>
        <tr>
            <td>
                <ul>
                    <li><b>Cuentaxpagar::list_cuentaxpagar_date()</b></li>
                    <li>Lista generada desde la tabla cuentaxpagars</li>
                    <li>Ordenada por fecha de vencimiento</li>
                    <li>Excluye cuotas canceladas</li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Paso 2: Filtros Adicionales -->
    <h2>Paso 2: Filtros Específicos</h2>
    
    <h3>Filtros Disponibles</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td width="50%" valign="top">
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr bgcolor="#f0f0f0">
                        <th>Plan de Pago</th>
                    </tr>
                    <tr>
                        <td>
                            <p><b>Propósito:</b> Filtrar por plan de pago específico</p>
                            <ul>
                                <li>Opcional - muestra todos si no se selecciona</li>
                                <li>Lista generada desde Planpago::list_planpago()</li>
                                <li>Aplica filtro en consulta principal</li>
                                <li>Útil para reportes por modalidad de pago</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" valign="top">
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr bgcolor="#f0f0f0">
                        <th>Grado Académico</th>
                    </tr>
                    <tr>
                        <td>
                            <p><b>Propósito:</b> Filtrar estudiantes por grado</p>
                            <ul>
                                <li>Opcional - muestra todos si no se selecciona</li>
                                <li>Lista generada desde Grado::list_pestudio_grado()</li>
                                <li>Filtro aplicado mediante join con seccions</li>
                                <li>Ideal para reportes por nivel educativo</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <h3>Estructura de Consulta</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%" bgcolor="#fff3cd">
        <tr>
            <td>
                <b>Relaciones Utilizadas:</b>
                <br><br>
                representants → estudiants → administrativas → planpagos → cuentaxpagars
                <br>+ inscripcions → seccions (para filtro por grado)
            </td>
        </tr>
    </table>

    <!-- Paso 3: Análisis de Resultados -->
    <h2>Paso 3: Interpretación de Resultados</h2>
    
    <h3>Estructura de la Tabla</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr bgcolor="#f0f0f0">
            <th>Columna</th>
            <th>Descripción</th>
            <th>Fórmula/Cálculo</th>
            <th>Importancia</th>
        </tr>
        <tr>
            <td><b>Representante/Estudiantes</b></td>
            <td>Información del representante y estudiantes asociados</td>
            <td>Datos básicos</td>
            <td><b>ALTA</b></td>
        </tr>
        <tr>
            <td><b>CI</b></td>
            <td>Cédulas de identidad</td>
            <td>Representante + cada estudiante</td>
            <td><b>MEDIA</b></td>
        </tr>
        <tr>
            <td><b>Deuda</b></td>
            <td>Monto pendiente por concepto</td>
            <td>TotalExchangeMontoCuentasXPagarAdeudado()</td>
            <td><b>ALTA</b></td>
        </tr>
        <tr>
            <td><b>ABN</b></td>
            <td>Total de abonos del representante</td>
            <td>total_abono_exchange</td>
            <td><b>MEDIA</b></td>
        </tr>
        <tr>
            <td><b>CAF</b></td>
            <td>Total de créditos a favor</td>
            <td>total_credito_exchange</td>
            <td><b>MEDIA</b></td>
        </tr>
        <tr>
            <td><b>SAF</b></td>
            <td>Saldo a favor del representante</td>
            <td>CAF + ABN</td>
            <td><b>ALTA</b></td>
        </tr>
        <tr>
            <td><b>D.Total</b></td>
            <td>Deuda total después de aplicar SAF</td>
            <td>Deuda - SAF (si > 0)</td>
            <td><b>CRÍTICA</b></td>
        </tr>
    </table>
    
    <h3>Indicadores Visuales</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td width="50%" valign="top">
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr bgcolor="#f0f0f0">
                        <th>Deuda Pendiente</th>
                    </tr>
                    <tr>
                        <td>
                            <p>Cuando <b>D.Total > 0</b>:</p>
                            <ul>
                                <li>Fila con borde destacado</li>
                                <li>Texto en negrita</li>
                                <li>Color rojo en montos significativos</li>
                                <li>Prioridad para seguimiento</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" valign="top">
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr bgcolor="#f0f0f0">
                        <th>Sin Deuda</th>
                    </tr>
                    <tr>
                        <td>
                            <p>Cuando <b>D.Total ≤ 0</b>:</p>
                            <ul>
                                <li>Fila con fondo atenuado</li>
                                <li>Indica saldo a favor suficiente</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <h3>Métricas del Reporte</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%" bgcolor="#e3f2fd">
        <tr>
            <td>
                <b>Cálculos Automáticos:</b>
                <ul>
                    <li><b>Total General:</b> Suma de todas las deudas pendientes</li>
                    <li><b>Representantes Deudores:</b> Porcentaje calculado vs total de representantes</li>
                    <li><b>Morosidad:</b> (representantes_con_deuda / total_representantes) * 100</li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Paso 4: Exportación y Acciones -->
    <h2>Paso 4: Exportación y Acciones</h2>
    
    <h3>Opciones de Exportación</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td width="33%" align="center">
                <table border="1" cellpadding="8" cellspacing="0" width="90%">
                    <tr>
                        <td align="center">
                            <b>Excel</b>
                            <br><small>Formato .xlsx</small>
                            <br><br>
                            <small><b>Todos los datos</b></small>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="33%" align="center">
                <table border="1" cellpadding="8" cellspacing="0" width="90%">
                    <tr>
                        <td align="center">
                            <b>CSV</b>
                            <br><small>Formato .csv</small>
                            <br><br>
                            <small><b>Datos estructurados</b></small>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="33%" align="center">
                <table border="1" cellpadding="8" cellspacing="0" width="90%">
                    <tr>
                        <td align="center">
                            <b>PDF</b>
                            <br><small>Formato .pdf</small>
                            <br><br>
                            <small><b>Columnas seleccionadas</b></small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <h3>Configuración DataTables</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%" bgcolor="#fff3cd">
        <tr>
            <td>
                <b>Características de la Tabla:</b>
                <ul>
                    <li><b>Paginación:</b> 10, 25, 50, 100, 500 registros o "Todos"</li>
                    <li><b>Búsqueda:</b> Filtrado en tiempo real</li>
                    <li><b>Ordenamiento:</b> Click en cabeceras de columna</li>
                    <li><b>Responsive:</b> Adaptable a dispositivos móviles</li>
                    <li><b>Columnas:</b> Visibilidad configurable</li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Optimización del Sistema -->
    <h2>Optimización y Rendimiento</h2>
    
    <h3>Características de Rendimiento</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td width="50%" valign="top">
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr bgcolor="#f0f0f0">
                        <th>Optimizaciones Implementadas</th>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li><b>Exclusión de recargos:</b> WHERE quota_original_id IS NULL</li>
                                <li><b>Filtro por estudiantes activos:</b> status_active = 'true'</li>
                                <li><b>Agrupación eficiente:</b> GROUP BY ci_representant</li>
                                <li><b>Joins optimizados:</b> Solo tablas necesarias</li>
                                <li><b>Límite opcional:</b> limit(20) para pruebas</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" valign="top">
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr bgcolor="#f0f0f0">
                        <th>Estructura de Consulta</th>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li><b>7 tablas relacionadas</b> en joins</li>
                                <li><b>Filtros aplicados</b> en consulta principal</li>
                                <li><b>Cálculos en modelo</b> no en consulta</li>
                                <li><b>Collections</b> para manipulación en PHP</li>
                                <li><b>Eager loading</b> para relaciones</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <h3>Recomendaciones para Grandes Volúmenes</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%" bgcolor="#fff3cd">
        <tr>
            <td>
                <b>Para instituciones con muchos datos:</b>
                <ul>
                    <li>Utilizar filtros específicos (plan de pago, grado) para reducir resultados</li>
                    <li>Exportar a Excel para análisis fuera del SAEFL</li>
                    <li>Programar reportes en horarios de baja demanda</li>
                    <li>Considerar paginación más agresiva (50 registros por página)</li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Preguntas Frecuentes -->
    <h2>Preguntas Frecuentes (FAQ)</h2>
    
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr bgcolor="#f0f0f0">
            <th width="30%">Pregunta</th>
            <th width="70%">Respuesta</th>
        </tr>
        <tr>
            <td valign="top"><b>¿Por qué no veo resultados al seleccionar una mensualidad?</b></td>
            <td>
                <b>Posibles causas:</b>
                <ul>
                    <li>No hay Conceptos por cobrar generadas para ese periodo</li>
                    <li>Todas las cuotas están completamente pagadas</li>
                    <li>Los representantes tienen saldo a favor que cubre las deudas</li>
                    <li>No hay estudiantes activos en el SAEFL</li>
                    <li>Los filtros adicionales están excluyendo todos los resultados</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td valign="top"><b>¿Cómo se calcula el "Saldo a Favor" (SAF)?</b></td>
            <td>
                <b>SAF = Total Abonos + Total Créditos a Favor</b>
                <br><br>
                <b>Donde:</b>
                <ul>
                    <li><b>Total Abonos:</b> Pagos anticipados o excedentes de pagos anteriores (total_abono_exchange)</li>
                    <li><b>Total Créditos:</b> Montos a favor por diversos conceptos (total_credito_exchange)</li>
                </ul>
                Este saldo se aplica automáticamente para reducir la deuda total.
            </td>
        </tr>
        <tr>
            <td valign="top"><b>¿Por qué algunas filas aparecen en rojo?</b></td>
            <td>
                Las filas en rojo indican que el <b>Saldo a Favor es mayor o igual a la deuda</b>, por lo que técnicamente no hay deuda pendiente. Esto puede ocurrir cuando:
                <ul>
                    <li>El representante tiene créditos suficientes para cubrir la deuda</li>
                    <li>Hay abonos disponibles que superan el monto adeudado</li>
                    <li>Combinación de ambos recursos</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td valign="top"><b>¿Puedo ver deudas de meses anteriores?</b></td>
            <td><b>SÍ</b>, seleccionando la mensualidad correspondiente en el filtro principal. El listado mostrará todas las deudas pendientes para ese periodo específico.</td>
        </tr>
        <tr>
            <td valign="top"><b>¿Los datos se actualizan en tiempo real?</b></td>
            <td><b>SÍ</b>, los datos reflejan el estado actual del sistema al momento de generar el reporte. Cualquier pago registrado se verá reflejado inmediatamente.</td>
        </tr>
    </table>

    <!-- Pie de Página -->
    <br>
    <hr>
    <center>
        <p><small>Documentación de Consulta de Deudas por Mensualidad</small></p>
        <p><small>Guía de referencia para usuarios - Versión Imprimible</small></p>
    </center>

</div>