
<div class="container-fluid">
    
    <!-- CABECERA DE LA GUÍA -->
    <div class="jumbotron py-1 my-1">
        <h3 class="">Proceso mensual para la limpieza y mantenimiento del dispositivo LFPA8503</h3>
        <hr class="my-2">
        <p>Restablecimiento de Fábrica, Restauración de Usuarios y Huellas</p>
    </div>

    <div class="row">

        <!-- CONTENIDO PRINCIPAL -->
        <div class="col-md-12">
            <!-- INTRODUCCIÓN -->
            <section id="introduccion" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-play-circle text-primary"></i>
                    Introducción
                </h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <p class="lead">
                            Esta guía técnica describe el proceso mensual para la limpieza y mantenimiento del terminal de control de acceso LFPA8503, incluyendo restablecimiento de fábrica y restauración de usuarios y huellas.
                        </p>
                        
                        <div class="tip-box">
                            <strong><i class="fas fa-lightbulb"></i> Propósito del Proceso:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Mantener el rendimiento óptimo del dispositivo</li>
                                <li>Realizar limpieza periódica de la configuración</li>
                                <li>Preservar los datos de usuarios y huellas durante el proceso</li>
                                <li>Garantizar la continuidad operativa del sistema de control de acceso</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-fingerprint fa-3x text-primary mb-3"></i>
                                <h5>Características Principales</h5>
                                <span class="badge badge-primary feature-badge">Restablecimiento Seguro</span>
                                <span class="badge badge-success feature-badge">Backup de Huellas</span>
                                <span class="badge badge-info feature-badge">Formato FAT32</span>
                                <span class="badge badge-warning feature-badge">Proceso Mensual</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RECOMENDACIONES USB -->
            <section id="recomendaciones-usb" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-usb text-primary"></i>
                    Recomendaciones para el Dispositivo USB
                </h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <p>Para garantizar el éxito en el proceso de respaldo y restauración, es fundamental utilizar un dispositivo USB compatible:</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-custom">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="30%">Parámetro</th>
                                        <th width="40%">Requisito</th>
                                        <th width="30%">Formato Recomendado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Capacidad</strong></td>
                                        <td>Mínimo 2 GB de almacenamiento</td>
                                        <td>2 GB - 32 GB</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estado</strong></td>
                                        <td>Totalmente vacío antes de usarlo</td>
                                        <td>Sin archivos previos</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Formato</strong></td>
                                        <td>FAT32 para compatibilidad</td>
                                        <td>FAT32 (obligatorio)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="warning-box">
                            <strong><i class="fas fa-exclamation-triangle"></i> Advertencia:</strong>
                            Un dispositivo USB con otros formatos (como NTFS o exFAT) puede no ser reconocido correctamente por el terminal LFPA8503.
                        </div>
                        
                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Consejo:</strong>
                            Formatee el dispositivo USB desde Windows usando la opción "Formato rápido" con sistema de archivos FAT32.
                        </div>
                    </div>
                </div>
            </section>

            <!-- GUÍA 1: RESTABLECIMIENTO -->
            <section id="guia1" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-cog text-primary"></i>
                    Guía 1: Restablecimiento de Fábrica LFPA8503
                </h2>
                
                <div class="alert alert-primary">
                    <strong>Objetivo:</strong> Realizar un restablecimiento de fábrica del terminal de control de acceso LFPA8503, manteniendo un respaldo seguro de los usuarios y huellas dactilares registrados.
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- PASO 1 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">1</div>
                                    <h4 class="mb-0">Acceder al menú principal del dispositivo</h4>
                                </div>
                                <ol>
                                    <li>Presione y mantenga el botón <strong>OK</strong> durante 3 segundos para iniciar sesión.</li>
                                    <li>Ingrese la contraseña del dispositivo (por defecto es la usada durante la activación).</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 2 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">2</div>
                                    <h4 class="mb-0">Ir a Gestión de Usuarios (User Management)</h4>
                                </div>
                                <ol>
                                    <li>En la pantalla principal, seleccione <strong>User_Management</strong>.</li>
                                    <li>Luego, seleccione <strong>User</strong>.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 3 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">3</div>
                                    <h4 class="mb-0">Exportar datos de usuarios y huellas</h4>
                                </div>
                                <ol>
                                    <li>Seleccione <strong>Export</strong>.</li>
                                    <li>Active las siguientes opciones:
                                        <ul>
                                            <li><strong>User</strong></li>
                                            <li><strong>Fingerprint</strong></li>
                                            <li>Opcional: <strong>Card</strong>, <strong>Password</strong></li>
                                        </ul>
                                    </li>
                                    <li>Seleccione <strong>USB_Storage</strong> como destino de exportación.</li>
                                    <li>Confirme con <strong>OK</strong> y espere a que finalice la exportación.</li>
                                </ol>
                                <div class="warning-box">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong> Asegúrese de tener conectado un dispositivo USB compatible (formato FAT32 recomendado) antes de realizar este paso.
                                </div>
                            </div>
                        </div>
                        
                        <!-- PASO 4 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">4</div>
                                    <h4 class="mb-0">Verificar el archivo de backup</h4>
                                </div>
                                <ol>
                                    <li>Retire el USB y verifique que el archivo fue creado correctamente.</li>
                                    <li>El archivo se guardará en una carpeta llamada <strong>UserBackup</strong> o similar.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 5 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">5</div>
                                    <h4 class="mb-0">Acceder a Configuración del sistema</h4>
                                </div>
                                <ol>
                                    <li>En la pantalla principal, seleccione <strong>System</strong>.</li>
                                    <li>Luego, seleccione <strong>Device_Information</strong>.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 6 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">6</div>
                                    <h4 class="mb-0">Seleccionar Factory Defaults</h4>
                                </div>
                                <ol>
                                    <li>Desplácese hasta encontrar la opción <strong>Restore_Default_Settings</strong>.</li>
                                    <li>Seleccione <strong>Factory Defaults</strong>.</li>
                                    <li>Confirme la acción cuando se le solicite.</li>
                                </ol>
                                <div class="tip-box">
                                    <strong><i class="fas fa-info-circle"></i> Nota:</strong> Esta acción eliminará toda la configuración personalizada, pero mantendrá la dirección IP actual si fue modificada previamente.
                                </div>
                            </div>
                        </div>
                        
                        <!-- PASO 7 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">7</div>
                                    <h4 class="mb-0">Reiniciar el dispositivo</h4>
                                </div>
                                <ol>
                                    <li>Una vez aplicados los valores predeterminados, el dispositivo se reiniciará automáticamente.</li>
                                <li>Espere a que el reinicio se complete antes de proceder con la restauración.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-microchip fa-3x text-primary mb-3"></i>
                                <p class="mb-1"><strong>Terminal LFPA8503</strong></p>
                                <small class="text-muted">Dispositivo de control de acceso</small>
                            </div>
                        </div>
                        
                        <div class="screenshot text-center mt-4">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-sd-card fa-3x text-warning mb-3"></i>
                                <p class="mb-1"><strong>Dispositivo USB</strong></p>
                                <small class="text-muted">Formato FAT32 requerido</small>
                            </div>
                        </div>
                        
                        <div class="success-box mt-4">
                            <strong><i class="fas fa-check-circle"></i> Proceso Completado:</strong> Una vez reiniciado el dispositivo, proceda con la Guía 2 para restaurar los usuarios y huellas.
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted">© 2025 - Guía técnica para HiKvision LFPA8503, Adaptada según el manual oficial del producto.</p>
                </div>
            </section>

            <!-- GUÍA 2: RESTAURACIÓN -->
            <section id="guia2" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-redo text-primary"></i>
                    Guía 2: Restauración de Usuarios y Huellas LFPA8503
                </h2>
                
                <div class="alert alert-primary">
                    <strong>Objetivo:</strong> Restaurar los usuarios y sus huellas dactilares previamente respaldados desde la interfaz local del terminal LFPA8503, tras haber realizado un restablecimiento de fábrica.
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- PASO 1 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">1</div>
                                    <h4 class="mb-0">Conectar el dispositivo USB con el backup</h4>
                                </div>
                                <ol>
                                    <li>Inserte en el puerto USB del equipo el dispositivo donde realizó la copia de seguridad.</li>
                                    <li>Asegúrese de que el archivo de backup esté accesible dentro del dispositivo USB.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 2 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">2</div>
                                    <h4 class="mb-0">Acceder al menú principal del equipo</h4>
                                </div>
                                <ol>
                                    <li>Presione y mantenga el botón <strong>OK</strong> durante 3 segundos para iniciar sesión.</li>
                                    <li>Introduzca la contraseña por defecto o la nueva si ya fue reconfigurada.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 3 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">3</div>
                                    <h4 class="mb-0">Navegar a la sección de gestión de usuarios</h4>
                                </div>
                                <ol>
                                    <li>En la pantalla principal, seleccione <strong>User Management</strong>.</li>
                                    <li>Luego, seleccione <strong>User</strong>.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 4 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">4</div>
                                    <h4 class="mb-0">Seleccionar opción de importación</h4>
                                </div>
                                <ol>
                                    <li>En la pantalla de lista de usuarios, seleccione la opción <strong>Import</strong>.</li>
                                    <li>Aparecerá una ventana mostrando las opciones de origen: - Seleccione <strong>USB Storage</strong> como dispositivo de lectura.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 5 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">5</div>
                                    <h4 class="mb-0">Seleccionar tipo de datos a importar</h4>
                                </div>
                                <ol>
                                    <li>Active las siguientes opciones según lo que desee restaurar:
                                        <ul>
                                            <li><strong>User</strong> – Para importar los usuarios registrados.</li>
                                            <li><strong>Fingerprint</strong> – Para importar las huellas dactilares asociadas.</li>
                                            <li>Opcional: <strong>Card</strong>, <strong>Password</strong></li>
                                        </ul>
                                    </li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 6 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">6</div>
                                    <h4 class="mb-0">Confirmar la importación</h4>
                                </div>
                                <ol>
                                    <li>Una vez seleccionados los tipos de datos, confirme con <strong>OK</strong>.</li>
                                    <li>Espere a que el proceso finalice. Aparecerá un mensaje indicando éxito o error.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <!-- PASO 7 -->
                        <div class="card step-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="step-number">7</div>
                                    <h4 class="mb-0">Verificar la restauración</h4>
                                </div>
                                <ol>
                                    <li>Regrese a la sección <strong>User List</strong>.</li>
                                    <li>Confirme que los usuarios aparecen listados con sus respectivas huellas.</li>
                                    <li>Realice una prueba de registro con uno de los usuarios restaurados.</li>
                                    <li>Coloque el dedo en el sensor para verificar que la huella sea reconocida correctamente.</li>
                                </ol>
                                <div class="warning-box">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong> Si hay conflicto de IDs duplicados, el sistema puede mostrar errores. Asegúrese de que el backup corresponde exactamente al modelo y versión del dispositivo actualizado.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-download fa-3x text-success mb-3"></i>
                                <p class="mb-1"><strong>Proceso de Restauración</strong></p>
                                <small class="text-muted">Importar desde USB</small>
                            </div>
                        </div>
                        
                        <div class="screenshot text-center mt-4">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-users fa-3x text-info mb-3"></i>
                                <p class="mb-1"><strong>Usuarios Restaurados</strong></p>
                                <small class="text-muted">Verificar en lista</small>
                            </div>
                        </div>
                        
                        <div class="success-box mt-4">
                            <strong><i class="fas fa-check-circle"></i> Proceso Completado:</strong> Una vez verificada la restauración, el dispositivo está listo para su uso normal con todos los usuarios y huellas recuperados.
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted">© 2025 - Guía técnica para Hikvision DS-K1T804EF. Adaptada según el manual oficial del producto.</p>
                </div>
            </section>

            <!-- PREGUNTAS FRECUENTES -->
            <section id="faq" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-question-circle text-primary"></i>
                    Preguntas Frecuentes (FAQ)
                </h2>
                
                <div class="accordion" id="faqAccordion">
                    <!-- PREGUNTA 1 -->
                    <div class="card">
                        <div class="card-header" id="faq1">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#answer1">
                                    <i class="fas fa-question"></i> ¿Por qué es necesario realizar este proceso mensualmente?
                                </button>
                            </h5>
                        </div>
                        <div id="answer1" class="collapse show" data-parent="#faqAccordion">
                            <div class="card-body">
                                El restablecimiento mensual ayuda a mantener el rendimiento óptimo del dispositivo, elimina configuraciones temporales que puedan causar inestabilidad y garantiza que el sistema opere de manera eficiente en el control de acceso.
                            </div>
                        </div>
                    </div>
                    
                    <!-- PREGUNTA 2 -->
                    <div class="card">
                        <div class="card-header" id="faq2">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer2">
                                    <i class="fas fa-question"></i> ¿Qué sucede si no tengo un dispositivo USB con formato FAT32?
                                </button>
                            </h5>
                        </div>
                        <div id="answer2" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                El terminal LFPA8503 solo reconoce dispositivos USB con formato FAT32. Si utiliza otro formato (NTFS, exFAT, etc.), el dispositivo no podrá leer ni escribir en el USB. Deberá formatear el dispositivo a FAT32 antes de proceder con el backup o restauración.
                            </div>
                        </div>
                    </div>
                    
                    <!-- PREGUNTA 3 -->
                    <div class="card">
                        <div class="card-header" id="faq3">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer3">
                                    <i class="fas fa-question"></i> ¿Se pierde la dirección IP configurada durante el restablecimiento?
                                </button>
                            </h5>
                        </div>
                        <div id="answer3" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                No, el restablecimiento de fábrica mantiene la dirección IP actual si fue modificada previamente. Solo se eliminan las configuraciones personalizadas, usuarios y huellas, pero la configuración de red se preserva.
                            </div>
                        </div>
                    </div>
                    
                    <!-- PREGUNTA 4 -->
                    <div class="card">
                        <div class="card-header" id="faq4">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer4">
                                    <i class="fas fa-question"></i> ¿Qué hago si aparece un error de IDs duplicados durante la restauración?
                                </button>
                            </h5>
                        </div>
                        <div id="answer4" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                Este error ocurre cuando el archivo de backup no corresponde exactamente al modelo y versión del dispositivo. Asegúrese de que está utilizando el backup correcto. Si el problema persiste, puede ser necesario contactar al soporte técnico de Hikvision.
                            </div>
                        </div>
                    </div>
                    
                    <!-- PREGUNTA 5 -->
                    <div class="card">
                        <div class="card-header" id="faq5">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer5">
                                    <i class="fas fa-question"></i> ¿Es posible realizar el backup y restauración por red en lugar de USB?
                                </button>
                            </h5>
                        </div>
                        <div id="answer5" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                Esta guía se centra en el método local mediante USB, que es el más confiable para este proceso. Algunos modelos pueden ofrecer opciones de backup por red a través del software de gestión, pero el método USB garantiza independencia de la conectividad de red.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.restablecimientoBIO') }}', '_blank')">
                <i class="fas fa-print"></i> Versión Imprimible
            </button>
            
        </div>
    </div>

</div>