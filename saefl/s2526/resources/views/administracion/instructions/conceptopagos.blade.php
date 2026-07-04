<main role="main" class="col-md-12 ml-sm-auto col-lg-12 px-0">
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="jumbotron alert-secondary py-2 mb-2">
            <h3 class="mb-1"><i class="fas fa-receipt text-success"></i> Gestión de Cuentas de Cobro</h3>
            <p class="lead mb-0">Administración detallada de conceptos individuales de pago dentro del sistema de cuentas
                por cobrar</p>
        </div>

        <div class="row">

            {{-- CONTENIDO --}}
            <div class="col-md-12">
                {{-- INTRO --}}
                <section id="intro" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-receipt text-success"></i> Introducción</h5>
                    <p class="small">Este módulo permite <strong>gestionar conceptos individuales de cobro</strong> que
                        componen las cuentas por pagar, definiendo montos específicos, nombres de concepto y estados de
                        asociación. Incluye funcionalidades completas de CRUD con filtros avanzados.</p>

                    <div class="alert alert-info py-1 small">
                        <strong><i class="fas fa-lightbulb"></i> Jerarquía:</strong> Plan de Pago → Cuenta por Pagar →
                        <strong>Cuentas de Cobro</strong> → Registros de Pago
                    </div>
                </section>

                {{-- 1. FILTRAR CONCEPTOS --}}
                <section id="filtros" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-filter text-primary"></i> 1. Filtrar Cuentas de Cobro</h5>

                    <table class="table table-borderless table-sm small">
                        <thead class="thead-light">
                            <tr>
                                <th>Campo</th>
                                <th>Uso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><kbd>Plan de Pago</kbd></td>
                                <td>Filtrar por plan de pago específico</td>
                            </tr>
                            <tr>
                                <td><kbd>Tipo</kbd></td>
                                <td>
                                    <span class="badge badge-success feature-badge">GENERAL</span>
                                    <span class="badge badge-info feature-badge">INDIVIDUAL</span>
                                </td>
                            </tr>
                            <tr>
                                <td><kbd>Asociación</kbd></td>
                                <td>
                                    <span class="badge badge-primary feature-badge">Asociado</span> (con pagos
                                    registrados)
                                    <span class="badge badge-secondary feature-badge">No asociado</span> (sin pagos)
                                </td>
                            </tr>
                            <tr>
                                <td><kbd>CI Estudiante</kbd></td>
                                <td>Buscar por cédula del estudiante (debounce 500ms)</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="alert alert-light border-left-primary py-1 small">
                        <strong>Tip:</strong> Use el botón <strong>"Nueva Cuenta"</strong> en el header para crear
                        cuentas directamente desde esta pantalla.
                    </div>
                </section>

                {{-- 2. ESTADÍSTICAS --}}
                <section id="estadisticas" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-chart-bar text-success"></i> 2. Interpretar estadísticas</h5>

                    <div class="row small">
                        <div class="col-md-3">
                            <span class="badge badge-primary">Total: X</span> Conceptos registrados
                        </div>
                        <div class="col-md-3">
                            <span class="badge badge-success">General: X</span> Conceptos de tipo GENERAL
                        </div>
                        <div class="col-md-3">
                            <span class="badge badge-info">Individual: X</span> Conceptos de tipo INDIVIDUAL
                        </div>
                        <div class="col-md-3">
                            <span class="badge badge-warning">Total: $X</span> Monto total en USD
                        </div>
                    </div>
                </section>

                {{-- 3. INTERPRETAR LA TABLA --}}
                <section id="tabla" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-table text-info"></i> 3. Interpretar la tabla de conceptos</h5>

                    <table class="table table-sm table-borderless small">
                        <thead class="thead-light">
                            <tr>
                                <th>Columna</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><kbd>ID</kbd></td>
                                <td>Identificador único del concepto</td>
                            </tr>
                            <tr>
                                <td><kbd>Concepto</kbd></td>
                                <td>
                                    Nombre del concepto + <br>
                                    <small class="text-muted">Tipo (GENERAL/INDIVIDUAL)</small>
                                </td>
                            </tr>
                            <tr>
                                <td><kbd>Cuenta</kbd></td>
                                <td>
                                    Nombre de la cuenta padre + <br>
                                    <small class="text-muted">Fecha de vencimiento</small>
                                </td>
                            </tr>
                            <tr>
                                <td><kbd>Plan de Pago</kbd></td>
                                <td>Plan al que pertenece la cuenta</td>
                            </tr>
                            <tr>
                                <td><kbd>Estudiante (CI)</kbd></td>
                                <td>Cédula del estudiante (solo INDIVIDUAL)</td>
                            </tr>
                            <tr>
                                <td><kbd>Monto USD</kbd></td>
                                <td>Valor del concepto en dólares</td>
                            </tr>
                            <tr>
                                <td><kbd>Estado</kbd></td>
                                <td>
                                    <span class="badge badge-success">-ASOCIADO-</span> (con pagos) <br>
                                    <span class="badge badge-secondary">-NO ASOCIADO-</span> (sin pagos)<br>
                                    <small class="text-muted">Estado Activo/Inactivo</small>
                                </td>
                            </tr>
                            <tr>
                                <td><kbd>Acciones</kbd></td>
                                <td>
                                    <button class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></button>
                                    Editar<br>
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                    Eliminar<br>
                                    <small class="text-muted">Botones habilitados/deshabilitados según permisos</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                {{-- 4. CREAR NUEVOS CONCEPTOS --}}
                <section id="crear" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-plus-circle text-success"></i> 4. Crear nuevos Cuentas de Cobro
                    </h5>

                    <p class="small"><strong>Procedimiento desde el módulo:</strong></p>

                    <ol class="small">
                        <li>Haga click en <button class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i>
                                Nueva Cuenta</button> en el header</li>
                        <li>Complete el formulario en el modal:
                            <table class="table table-sm table-borderless mt-1">
                                <tr>
                                    <td><kbd>Tipo de Cuenta</kbd></td>
                                    <td>
                                        <strong>Select list con opciones:</strong><br>
                                        ● GENERAL - Para todos los estudiantes<br>
                                        ● INDIVIDUAL - Para estudiante específico
                                    </td>
                                </tr>
                                <tr>
                                    <td><kbd>Cuenta por Cobrar</kbd></td>
                                    <td>
                                        <strong>Lista dinámica:</strong><br>
                                        ● Para GENERAL: muestra solo cuentas GENERALES<br>
                                        ● Para INDIVIDUAL: muestra solo cuentas INDIVIDUALES
                                    </td>
                                </tr>
                                <tr>
                                    <td><kbd>Concepto de Pago</kbd></td>
                                    <td>Seleccione de la lista predefinida de conceptos</td>
                                </tr>
                                <tr>
                                    <td><kbd>Monto USD</kbd></td>
                                    <td>Valor en dólares del concepto (numérico, mínimo 0.01)</td>
                                </tr>
                                <tr>
                                    <td><kbd>Estado</kbd></td>
                                    <td>Activo/Inactivo</td>
                                </tr>
                                <tr>
                                    <td><kbd>Descripción</kbd></td>
                                    <td>Detalles adicionales del concepto (obligatorio)</td>
                                </tr>
                                <tr>
                                    <td><kbd>Observaciones</kbd></td>
                                    <td>Notas internas opcionales</td>
                                </tr>
                                <tr>
                                    <td><kbd>Opciones</kbd></td>
                                    <td>
                                        ● Permite Descuento (checkbox)<br>
                                        ● Anualidad (checkbox)
                                    </td>
                                </tr>
                            </table>
                        </li>
                        <li>Presione <button class="btn btn-success btn-sm"><i class="fas fa-save"></i> Crear
                                Cuenta</button> para guardar</li>
                    </ol>

                    <div class="alert alert-info py-1 small">
                        <strong><i class="fas fa-lightbulb"></i> Característica inteligente:</strong> Al cambiar el tipo
                        (GENERAL/INDIVIDUAL), la lista de "Cuenta por Cobrar" se actualiza automáticamente y se limpia
                        la selección anterior.
                    </div>
                </section>

                {{-- 5. EDITAR CONCEPTOS EXISTENTES --}}
                <section id="editar" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-edit text-warning"></i> 5. Editar conceptos existentes</h5>

                    <p class="small"><strong>Procedimiento:</strong></p>

                    <ol class="small">
                        <li>Haga click en <button class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></button> en
                            la columna Acciones</li>
                        <li>Se abrirá el modal de edición con:
                            <table class="table table-sm table-borderless mt-1">
                                <tr>
                                    <td><kbd>Información de Cuenta</kbd></td>
                                    <td>Datos de solo lectura de la cuenta asociada</td>
                                </tr>
                                <tr>
                                    <td><kbd>Concepto de Pago</kbd></td>
                                    <td>Lista desplegable de conceptos</td>
                                </tr>
                                <tr>
                                    <td><kbd>Monto USD</kbd></td>
                                    <td>Valor editable del concepto</td>
                                </tr>
                                <tr>
                                    <td><kbd>Estado</kbd></td>
                                    <td>Activo/Inactivo</td>
                                </tr>
                                <tr>
                                    <td><kbd>Descripción</kbd></td>
                                    <td>Campo de texto editable</td>
                                </tr>
                                <tr>
                                    <td><kbd>Observaciones</kbd></td>
                                    <td>Campo de texto opcional</td>
                                </tr>
                            </table>
                        </li>
                        <li>Presione <button class="btn btn-warning btn-sm"><i class="fas fa-save"></i>
                                Actualizar</button> para guardar cambios</li>
                    </ol>

                    <div class="alert alert-warning py-1 small">
                        <strong><i class="fas fa-exclamation-triangle"></i> Restricción de edición:</strong> Solo
                        conceptos con <code>status_edit = true</code> pueden ser editados. Los botones estarán
                        deshabilitados para conceptos no editables.
                    </div>
                </section>

                {{-- 6. ELIMINAR CONCEPTOS --}}
                <section id="eliminar" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-trash text-danger"></i> 6. Eliminar concepto de cobro</h5>

                    <div class="alert alert-warning py-1 small">
                        <strong><i class="fas fa-exclamation-triangle"></i> Restricciones de eliminación:</strong>
                        <ul class="mb-0">
                            <li>No se puede eliminar si está <strong>-ASOCIADO-</strong> (tiene pagos registrados)</li>
                            <li>No se puede eliminar si pertenece a una cuenta <strong>GENERAL</strong></li>
                            <li>El botón estará <button class="btn btn-xs btn-danger" disabled><i
                                        class="fas fa-trash"></i></button> cuando no sea posible eliminar</li>
                        </ul>
                    </div>

                    <ol class="small">
                        <li>Verifique que el estado sea <span class="badge badge-secondary">-NO ASOCIADO-</span></li>
                        <li>Click en <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button> (si
                            está habilitado)</li>
                        <li>Confirme la eliminación en el cuadro de diálogo SweetAlert</li>
                        <li>Se mostrará confirmación de eliminación exitosa</li>
                    </ol>
                </section>

                {{-- 7. TIPOS DE CUENTA - GENERAL vs INDIVIDUAL --}}
                <section id="tipos" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-exchange-alt text-primary"></i> 7. Tipos de Cuenta: GENERAL vs
                        INDIVIDUAL</h5>

                    <div class="row small">
                        <div class="col-md-6">
                            <div class="card border-success mb-2">
                                <div class="card-header py-1 bg-success text-white text-center">
                                    <strong>GENERAL</strong>
                                </div>
                                <div class="card-body py-1">
                                    <p class="mb-1"><strong>Características:</strong></p>
                                    <ul class="mb-1">
                                        <li>Aplicable a todos los estudiantes</li>
                                        <li>No se puede eliminar</li>
                                        <li>Lista específica de cuentas</li>
                                        <li>Sin estudiante asociado</li>
                                    </ul>
                                    <p class="mb-0 text-success"><small><strong>Ejemplo:</strong> Matrícula general,
                                            Cuota de materiales</small></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info mb-2">
                                <div class="card-header py-1 bg-info text-white text-center">
                                    <strong>INDIVIDUAL</strong>
                                </div>
                                <div class="card-body py-1">
                                    <p class="mb-1"><strong>Características:</strong></p>
                                    <ul class="mb-1">
                                        <li>Para estudiante específico</li>
                                        <li>Se puede eliminar (si no asociado)</li>
                                        <li>Lista específica de cuentas</li>
                                        <li>Con CI de estudiante</li>
                                    </ul>
                                    <p class="mb-0 text-info"><small><strong>Ejemplo:</strong> Cuota especial, Pago
                                            extraordinario</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- 8. ESTADOS DE ASOCIACIÓN --}}
                <section id="asociacion" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-link text-primary"></i> 8. Estados de asociación</h5>

                    <div class="row small">
                        <div class="col-md-6">
                            <div class="card border-success mb-2">
                                <div class="card-header py-1 bg-success text-white">
                                    <strong>-ASOCIADO-</strong>
                                </div>
                                <div class="card-body py-1">
                                    <p class="mb-0">El concepto tiene <strong>pagos registrados</strong> vinculados a
                                        través de <code>conceptocancelados</code>.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-secondary mb-2">
                                <div class="card-header py-1 bg-secondary text-white">
                                    <strong>-NO ASOCIADO-</strong>
                                </div>
                                <div class="card-body py-1">
                                    <p class="mb-0">El concepto <strong>no tiene pagos registrados</strong> y puede
                                        ser eliminado.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border-left-primary py-1 small">
                        <strong>Nota:</strong> La asociación se crea automáticamente cuando se registra un pago para
                        este concepto.
                    </div>
                </section>

                {{-- 9. PAGINACIÓN Y NAVEGACIÓN --}}
                <section id="paginacion" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-cog text-secondary"></i> 9. Navegación y configuración</h5>

                    <div class="row small">
                        <div class="col-md-6">
                            <strong>Paginación:</strong>
                            <ul>
                                <li>Navegue entre páginas con los controles inferiores</li>
                                <li>Los filtros se mantienen al cambiar de página</li>
                                <li>Resultados ordenados por fecha de creación (más recientes primero)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Accesos rápidos:</strong>
                            <ul>
                                <li>Use el menú rápido para ir a otras secciones</li>
                                <li>Acceso directo a creación de conceptos</li>
                                <li>Navegación a cuentas por pagar relacionadas</li>
                            </ul>
                        </div>
                    </div>
                </section>

                {{-- FAQ --}}
                <section id="faq" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-question-circle text-primary"></i> Preguntas frecuentes</h5>

                    <div class="accordion" id="accordionFaq">
                        <div class="card border-0">
                            <div class="card-header p-1" id="fq1">
                                <button class="btn btn-sm btn-link" data-toggle="collapse" data-target="#ans1">
                                    ¿Cómo funciona la selección de tipos GENERAL/INDIVIDUAL?
                                </button>
                            </div>
                            <div id="ans1" class="collapse show" data-parent="#accordionFaq">
                                <div class="card-body py-1 small">
                                    Al crear un nuevo concepto:
                                    <ul class="mb-0">
                                        <li>Seleccione el <strong>tipo</strong> en el dropdown</li>
                                        <li>La lista de <strong>Cuentas por Cobrar</strong> se actualiza automáticamente
                                        </li>
                                        <li>Para GENERAL: solo muestra cuentas de tipo GENERAL</li>
                                        <li>Para INDIVIDUAL: solo muestra cuentas de tipo INDIVIDUAL</li>
                                        <li>La selección anterior se limpia al cambiar de tipo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0">
                            <div class="card-header p-1" id="fq2">
                                <button class="btn btn-sm btn-link collapsed" data-toggle="collapse"
                                    data-target="#ans2">
                                    ¿Por qué no puedo editar un concepto?
                                </button>
                            </div>
                            <div id="ans2" class="collapse" data-parent="#accordionFaq">
                                <div class="card-body py-1 small">
                                    Un concepto no puede ser editado cuando:
                                    <ul class="mb-0">
                                        <li>El campo <code>status_edit</code> es <code>false</code></li>
                                        <li>El botón de edición estará deshabilitado</li>
                                        <li>Se mostrará el texto "No editable"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0">
                            <div class="card-header p-1" id="fq3">
                                <button class="btn btn-sm btn-link collapsed" data-toggle="collapse"
                                    data-target="#ans3">
                                    ¿Qué significa "Asociado" vs "No asociado"?
                                </button>
                            </div>
                            <div id="ans3" class="collapse" data-parent="#accordionFaq">
                                <div class="card-body py-1 small">
                                    <strong>Asociado:</strong> El concepto tiene pagos registrados (relación
                                    <code>conceptocancelados</code>).<br>
                                    <strong>No asociado:</strong> El concepto no tiene pagos registrados y está
                                    disponible para eliminación.<br><br>
                                    Esta asociación se crea automáticamente al registrar un pago para el concepto.
                                </div>
                            </div>
                        </div>

                        <div class="card border-0">
                            <div class="card-header p-1" id="fq4">
                                <button class="btn btn-sm btn-link collapsed" data-toggle="collapse"
                                    data-target="#ans4">
                                    ¿Puedo cambiar el tipo de un concepto existente?
                                </button>
                            </div>
                            <div id="ans4" class="collapse" data-parent="#accordionFaq">
                                <div class="card-body py-1 small">
                                    <strong>No,</strong> el tipo de cuenta está determinado por la cuenta padre
                                    seleccionada y no puede ser modificado en la edición.<br><br>
                                    La información de la cuenta asociada se muestra como <strong>solo lectura</strong>
                                    en el modal de edición para mantener la integridad de los datos.
                                </div>
                            </div>
                        </div>

                        <div class="card border-0">
                            <div class="card-header p-1" id="fq5">
                                <button class="btn btn-sm btn-link collapsed" data-toggle="collapse"
                                    data-target="#ans5">
                                    ¿Qué diferencia hay entre "Conceptos de Cobro" y "Cuentas de Cobro"?
                                </button>
                            </div>
                            <div id="ans5" class="collapse" data-parent="#accordionFaq">
                                <div class="card-body py-1 small">
                                    <strong>Conceptos de Cobro:</strong> Contenedor principal con fechas, descripción,
                                    tipo y plan de pago.<br>
                                    <strong>Cuentas de Cobro:</strong> Elemento individual dentro de la cuenta con monto
                                    específico y concepto definido.<br><br>
                                    <strong>Ejemplo:</strong><br>
                                    • "Matrícula" (Concepto de Cobro)<br>
                                    • Contiene: "Inscripción", "Derecho de grado", "Materiales" (Cuentas de Cobro)
                                </div>
                            </div>
                        </div>
                    </div>{{-- /accordion --}}
                </section>

                <button type="button" class="btn btn-success"
                    onclick="window.open('{{ route('helpers.pdf.conceptopagos') }}', '_blank')">
                    <i class="fas fa-print"></i> Versión Imprimible
                </button>

            </div>{{-- /col-md-12 --}}
        </div>{{-- /row --}}
    </div>{{-- /container-fluid --}}
</main>
