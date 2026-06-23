<div class="tab-pane fade" id="export-print" role="tabpanel">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0"><i class="fas fa-print mr-2"></i>Caso de Uso: Exportación e Impresión de Formatos</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5>Descripción</h5>
                    <p class="text-muted">
                        Este caso de uso permite a los docentes descargar o imprimir documentos en formato PDF
                        listos para presentar oficialmente. El sistema genera documentos estandarizados con
                        formato profesional, incluyendo encabezados institucionales, contenido estructurado
                        y elementos de validación oficial.
                    </p>

                    <div class="mt-3">
                        <h6><i class="fas fa-list-ul mr-2"></i>Funcionalidades Principales:</h6>
                        <ul class="text-muted">
                            <li>Seleccionar y configurar documentos para exportación</li>
                            <li>Generar PDFs con formato oficial estandarizado</li>
                            <li>Configurar opciones de impresión y presentación</li>
                            <li>Descargar archivos para almacenamiento local</li>
                            <li>Imprimir directamente desde el navegador</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-user mr-2"></i>Actor Principal</h6>
                            <p class="mb-0">Docente</p>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-file-pdf mr-2"></i>Formatos Disponibles</h6>
                            <ul class="mb-0 small">
                                <li>PDF Oficial</li>
                                <li>Formato A4/Carta</li>
                                <li>Orientación Vertical/Horizontal</li>
                                <li>Con/Sin Encabezados</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-cogs mr-2"></i>Sistema Involucrado</h6>
                            <p class="mb-0">Sistema de Exportación</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagrama Principal -->
            <div class="diagram-container">
                <h5 class="text-center mb-3"><i class="fas fa-sitemap mr-2"></i>Diagrama de Casos de Uso</h5>
                <div class="mermaid">
                    graph TD
                        A["👤 Docente"] --> B["Seleccionar Documento"]
                        A --> C["Configurar Formato"]
                        A --> D["Generar PDF"]
                        A --> E["Imprimir Documento"]
                        A --> F["Descargar Archivo"]

                        B --> G["Planificación Semanal"]
                        B --> H["Planificación Quincenal"]
                        B --> I["Proyecto de Aula"]
                        B --> J["Informe Pedagógico"]
                        B --> K["Plan Especial"]
                        B --> L["Plan de Evaluación"]

                        C --> M["Seleccionar Orientación"]
                        C --> N["Configurar Márgenes"]
                        C --> O["Incluir Encabezados"]
                        C --> P["Agregar Pie de Página"]

                        D --> Q["Validar Datos Completos"]
                        D --> R["Aplicar Formato Oficial"]
                        D --> S["Generar Vista Previa"]

                        E --> T["Configurar Impresora"]
                        E --> U["Seleccionar Páginas"]
                        E --> V["Ajustar Calidad"]

                        W["🖨️ Sistema de Exportación"] --> X["Procesar Plantillas"]
                        W --> Y["Generar Documentos"]
                        W --> Z["Gestionar Descargas"]

                        style A fill:#e1f5fe
                        style W fill:#fff3e0
                        style B fill:#f5f5f5
                        style C fill:#e8f5e8
                        style D fill:#fff8e1
                </div>
            </div>

            <!-- Tipos de Documentos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Documentos Disponibles para Exportación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-calendar-week fa-2x text-success mb-2"></i>
                                            <h6>Planificación Semanal</h6>
                                            <p class="small text-muted mb-2">
                                                Formato con estrategias diarias, áreas de aprendizaje y vinculación con proyectos
                                            </p>
                                            <span class="badge badge-success">Eiplanningwk</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
                                            <h6>Planificación Quincenal</h6>
                                            <p class="small text-muted mb-2">
                                                Formato bimensual con resumen de actividades y componentes curriculares
                                            </p>
                                            <span class="badge badge-info">Eiplanningbwk</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-project-diagram fa-2x text-warning mb-2"></i>
                                            <h6>Proyecto de Aula</h6>
                                            <p class="small text-muted mb-2">
                                                Documento completo con diagnóstico, revisión y áreas de aprendizaje
                                            </p>
                                            <span class="badge badge-warning">Eiprojectk</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-clipboard-check fa-2x text-primary mb-2"></i>
                                            <h6>Plan de Evaluación</h6>
                                            <p class="small text-muted mb-2">
                                                Registro de actividades evaluativas con indicadores y observaciones
                                            </p>
                                            <span class="badge badge-primary">Eievaluationk</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                                            <h6>Plan Especial</h6>
                                            <p class="small text-muted mb-2">
                                                Documento para situaciones particulares con justificación y actividades
                                            </p>
                                            <span class="badge badge-danger">Eispecialk</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-alt fa-2x text-secondary mb-2"></i>
                                            <h6>Informe Pedagógico</h6>
                                            <p class="small text-muted mb-2">
                                                Informe final individual con logros, observaciones y recomendaciones
                                            </p>
                                            <span class="badge badge-secondary">Eifinalk</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proceso de Exportación -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-route mr-2"></i>Proceso de Exportación e Impresión</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-mouse-pointer fa-2x text-primary mb-2"></i>
                                            <h6>1. Selección</h6>
                                            <p class="small text-muted mb-0">
                                                Elegir el documento a exportar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                            <h6>2. Validación</h6>
                                            <p class="small text-muted mb-0">
                                                Verificar completitud de datos
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-cogs fa-2x text-info mb-2"></i>
                                            <h6>3. Configuración</h6>
                                            <p class="small text-muted mb-0">
                                                Ajustar formato y opciones
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-file-pdf fa-2x text-warning mb-2"></i>
                                            <h6>4. Generación</h6>
                                            <p class="small text-muted mb-0">
                                                Crear PDF con formato oficial
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-eye fa-2x text-secondary mb-2"></i>
                                            <h6>5. Vista Previa</h6>
                                            <p class="small text-muted mb-0">
                                                Revisar antes de finalizar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-download fa-2x text-danger mb-2"></i>
                                            <h6>6. Descarga/Impresión</h6>
                                            <p class="small text-muted mb-0">
                                                Obtener documento final
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opciones de Configuración -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-sliders-h mr-2"></i>Opciones de Configuración</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h6 class="text-info"><i class="fas fa-expand-arrows-alt mr-2"></i>Formato de Página</h6>
                                    <ul class="small text-muted">
                                        <li><strong>Tamaño:</strong> A4, Carta, Legal</li>
                                        <li><strong>Orientación:</strong> Vertical (Portrait) / Horizontal (Landscape)</li>
                                        <li><strong>Márgenes:</strong> Normal, Estrecho, Amplio, Personalizado</li>
                                    </ul>
                                </div>
                                <div class="col-12 mb-3">
                                    <h6 class="text-success"><i class="fas fa-header mr-2"></i>Elementos del Documento</h6>
                                    <ul class="small text-muted">
                                        <li><strong>Encabezado:</strong> Logo institucional, datos de la escuela</li>
                                        <li><strong>Pie de página:</strong> Fecha de generación, número de página</li>
                                        <li><strong>Marca de agua:</strong> Sello oficial (opcional)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-print mr-2"></i>Opciones de Impresión</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h6 class="text-warning"><i class="fas fa-printer mr-2"></i>Configuración de Impresora</h6>
                                    <ul class="small text-muted">
                                        <li><strong>Impresora:</strong> Selección automática o manual</li>
                                        <li><strong>Calidad:</strong> Borrador, Normal, Alta calidad</li>
                                        <li><strong>Color:</strong> Blanco y negro / Color</li>
                                    </ul>
                                </div>
                                <div class="col-12 mb-3">
                                    <h6 class="text-danger"><i class="fas fa-copy mr-2"></i>Opciones de Copia</h6>
                                    <ul class="small text-muted">
                                        <li><strong>Páginas:</strong> Todas, Rango específico, Páginas seleccionadas</li>
                                        <li><strong>Copias:</strong> Número de ejemplares</li>
                                        <li><strong>Intercalado:</strong> Ordenar copias múltiples</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validaciones y Controles -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-shield-alt mr-2"></i>Validaciones del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li><strong>Datos Completos:</strong> Verificar que todos los campos obligatorios estén llenos</li>
                                <li><strong>Fechas Válidas:</strong> Comprobar coherencia temporal</li>
                                <li><strong>Relaciones Correctas:</strong> Validar vínculos entre entidades</li>
                                <li><strong>Permisos de Usuario:</strong> Confirmar autorización para exportar</li>
                                <li><strong>Integridad de Datos:</strong> Verificar consistencia de información</li>
                                <li><strong>Formato Oficial:</strong> Aplicar plantillas institucionales</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-double mr-2"></i>Controles de Calidad</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li><strong>Vista Previa:</strong> Revisión antes de generar PDF final</li>
                                <li><strong>Formato Consistente:</strong> Aplicación uniforme de estilos</li>
                                <li><strong>Legibilidad:</strong> Verificar tamaño y claridad de texto</li>
                                <li><strong>Elementos Gráficos:</strong> Posicionamiento correcto de logos</li>
                                <li><strong>Paginación:</strong> Numeración y saltos de página apropiados</li>
                                <li><strong>Metadatos:</strong> Información del documento (autor, fecha, título)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tecnologías y Herramientas -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-tools mr-2"></i>Tecnologías y Herramientas Utilizadas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary"><i class="fab fa-laravel mr-2"></i>Backend (Laravel)</h6>
                                    <ul class="small text-muted">
                                        <li><strong>DomPDF:</strong> Generación de archivos PDF</li>
                                        <li><strong>Blade Templates:</strong> Plantillas para documentos</li>
                                        <li><strong>Storage:</strong> Gestión temporal de archivos</li>
                                        <li><strong>Response:</strong> Descarga y visualización</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fab fa-js-square mr-2"></i>Frontend (JavaScript)</h6>
                                    <ul class="small text-muted">
                                        <li><strong>Print.js:</strong> Control de impresión desde navegador</li>
                                        <li><strong>FileSaver.js:</strong> Descarga de archivos</li>
                                        <li><strong>PDF.js:</strong> Vista previa de documentos</li>
                                        <li><strong>Bootstrap:</strong> Interfaz de configuración</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning"><i class="fas fa-file-pdf mr-2"></i>Formato PDF</h6>
                                    <ul class="small text-muted">
                                        <li><strong>Estándares:</strong> PDF/A para archivado</li>
                                        <li><strong>Seguridad:</strong> Protección contra modificación</li>
                                        <li><strong>Metadatos:</strong> Información del documento</li>
                                        <li><strong>Accesibilidad:</strong> Compatibilidad con lectores</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Casos de Uso Específicos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-lightbulb mr-2"></i>Casos de Uso Específicos</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-primary"><i class="fas fa-user-tie mr-2"></i>Presentación a Directivos</h6>
                                            <p class="small text-muted">
                                                <strong>Formato:</strong> PDF con encabezados oficiales<br>
                                                <strong>Calidad:</strong> Alta resolución<br>
                                                <strong>Elementos:</strong> Logo, sellos, firmas digitales
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-success"><i class="fas fa-users mr-2"></i>Entrega a Padres</h6>
                                            <p class="small text-muted">
                                                <strong>Formato:</strong> PDF simplificado y claro<br>
                                                <strong>Idioma:</strong> Terminología accesible<br>
                                                <strong>Presentación:</strong> Diseño amigable y profesional
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-warning"><i class="fas fa-archive mr-2"></i>Archivo Institucional</h6>
                                            <p class="small text-muted">
                                                <strong>Formato:</strong> PDF/A para preservación<br>
                                                <strong>Metadatos:</strong> Información completa<br>
                                                <strong>Organización:</strong> Nomenclatura estandarizada
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
