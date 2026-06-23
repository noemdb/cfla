<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-info btn-sm mb-3" data-toggle="modal" data-target="#indicadoresModal">
    <i class="fas fa-info-circle"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="indicadoresModal" tabindex="-1" role="dialog" aria-labelledby="indicadoresModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="indicadoresModalLabel">
            <i class="fas fa-info-circle"></i> Descripción de Indicadores de Planificación
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body bg-light">
        <ul class="list-group list-group-flush">

            <li class="list-group-item bg-light">
                <strong class="text-info"><i class="fas fa-database"></i> Total de Registros</strong>
                <small class="d-block text-muted">
                    Refleja el número total de registros generados en el sistema de planificación educativa, incluyendo planes semanales, quincenales, proyectos, evaluaciones e informes. Este indicador proporciona una visión integral de la actividad documental y administrativa durante el período escolar.
                </small>
            </li>

            <li class="list-group-item bg-light">
                <strong class="text-success"><i class="fas fa-project-diagram"></i> Proyectos Activos</strong>
                <small class="d-block text-muted">
                    Indica cuántos proyectos de aula o institucionales están en desarrollo. Estos proyectos fomentan el trabajo en equipo, la creatividad y la aplicación de conocimientos en situaciones reales.
                </small>
            </li>

            <li class="list-group-item bg-light">
                <strong class="text-primary"><i class="fas fa-check-circle"></i> Evaluaciones Completadas</strong>
                <small class="d-block text-muted">
                    Mide la cantidad de evaluaciones aplicadas durante el período escolar. Este indicador ayuda a monitorear el avance en el proceso de valoración del aprendizaje.
                </small>
            </li>

            <li class="list-group-item bg-light">
                <strong class="text-warning"><i class="fas fa-calendar-week"></i> Plan Semanal</strong>
                <small class="d-block text-muted">
                    Representa el número de planes pedagógicos diseñados y ejecutados semanalmente, detallando actividades, recursos y metodologías para el desarrollo continuo del proceso educativo.
                </small>
            </li>

            <li class="list-group-item bg-light">
                <strong class="text-warning"><i class="fas fa-calendar-alt"></i> Plan Quincenal</strong>
                <small class="d-block text-muted">
                    Refleja la planificación de ciclos de dos semanas, integrando actividades y evaluaciones que permiten una visión más amplia y flexible del proceso pedagógico.
                </small>
            </li>

            <li class="list-group-item bg-light">
                <strong class="text-success"><i class="fas fa-bullseye"></i> Proyectos de Aula</strong>
                <small class="d-block text-muted">
                    Proyectos desarrollados dentro del aula que promueven el aprendizaje activo, el pensamiento crítico y la resolución de problemas mediante el trabajo colaborativo.
                </small>
            </li>

            <li class="list-group-item bg-light">
                <strong class="text-secondary"><i class="fas fa-clipboard-list"></i> Planes Especiales</strong>
                <small class="d-block text-muted">
                    Planificaciones diseñadas para atender necesidades educativas específicas, como programas de refuerzo o inclusión, adaptadas a contextos o estudiantes particulares.
                </small>
            </li>

            <li class="list-group-item bg-light">
                <strong class="text-primary"><i class="fas fa-chart-bar"></i> Plan de Evaluación</strong>
                <small class="d-block text-muted">
                    Número de planes que organizan los momentos, instrumentos y criterios de evaluación, asegurando el seguimiento sistemático de los logros estudiantiles.
                </small>
            </li>

            <li class="list-group-item bg-light">
                <strong class="text-danger"><i class="fas fa-file-alt"></i> Informes Finales (Pedagógicos)</strong>
                <small class="d-block text-muted">
                    Informes pedagógicos finales que integran y sintetizan las evidencias de aprendizaje recogidas durante el período, permitiendo una visión completa del rendimiento de cada estudiante.
                </small>
            </li>

        </ul>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
      
    </div>
  </div>
</div>
