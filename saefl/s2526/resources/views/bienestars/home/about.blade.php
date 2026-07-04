<div class="container-fluid">

    <div class="row">
        <div class="col-sm-6 px-1">
            <div class="card h-100">
                <div class="d-flex justify-content-center py-2">
                    <i class="{{ $icon_menus['student_records'] ?? '' }} fa-5x "></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-center">Módulo Bienestar Estudiantil</h5>
                    <p class="card-text">
                    <div>
                        En este módulo se puede dar un gestionamiento a la información del estudiante relacionada con la
                        <span class="font-weight-bold">Coordinación de Bienestar Estudiantil</span>.
                        El registro de información social, psicológica, de patología y las incidencias ocurridas en la
                        institución provee la capacidad de generar
                        un reporte bien estructurado, que presente aspectos de interés como:
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Qué porcentaje se tiene en relación al cumplimiento del esquema de
                            vacunación.</li>
                        <li class="list-group-item">Tendencia de las potencialidades deportivas y culturales.</li>
                        <li class="list-group-item">Que enfermedades graves son más frecuentes.</li>
                        <li class="list-group-item">Que condiciones especiales presentes en los estudiantes son más
                            frecuentes.</li>
                        <li class="list-group-item">Cuantos estudiantes presentan condiciones médicas considerables para
                            el proceso educativo.</li>
                        <li class="list-group-item">Cuál es la tendencia en cuanto al tipo y motivo de las incidencias
                            registradas.</li>
                        <li class="list-group-item">Área según la cual se orienta la mayor cantidad de incidencias.</li>
                        <li class="list-group-item">Un <span class="font-weight-bold">Historial Digital</span> para
                            cada estudiante contentivo de la información generada en este módulo.</li>
                    </ul>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 px-1">
            <div class="card h-100">
                <div class="d-flex justify-content-center py-2">
                    <i class="fas fa-project-diagram fa-5x"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-center">Descripciones Tabuladas</h5>
                    <p class="card-text">
                    <div>
                        La implementación de una estructura jerárquica con diferentes niveles de categorías (valoración, motivo y ámbito) ayuda a organizar una cantidad importante de descripciones tabuladas
                        de un incidente escolar de la siguiente manera:
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            Mejora la eficiencia del proceso de registro de una incidencia: Al tener una estructura jerárquica, los usuarios pueden seleccionar las categorías apropiadas para describir un incidente
                            con mayor rapidez y facilidad. Esto reduce el tiempo que se necesita para completar el formulario de registro, lo que puede ayudar a agilizar el proceso de investigación y resolución de
                            incidentes.
                        </li>
                        <li class="list-group-item">
                            Facilita la búsqueda de incidentes: La estructura jerárquica permite a los usuarios buscar incidentes por categoría, motivo o ámbito. Esto facilita la identificación de incidentes específicos.
                        </li>
                        </li>
                        <li class="list-group-item">
                            Mejora la precisión de los datos: La estructura jerárquica ayuda a garantizar que los datos de los incidentes se registren de manera consistente. Esto puede ayudar a mejorar la precisión de los informes y análisis de incidentes.
                        </li>
                    </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-lg-4 px-1">
            <div class="card h-100">
                <div class="d-flex justify-content-center py-2">
                    <i class="{{ $icon_menus['fichaDigital'] ?? '' }} fa-5x "></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-center">Ficha Estudiantil</h5>
                    <p class="card-text">
                        Presenta información social, psicológica y de patologías. Es particularmente útil en los
                        procesos de inscripción, dado que es el momento
                        de establecer las condiciones iniciales en las cuales el estudiante solicita ingresar a la
                        matrícula de la institución.

                        <div class="my-2"> @include('bienestars.home.modals.helps.student-record') </div>
                    </p>

                </div>
            </div>
        </div>

        <div class="col-lg-4 px-1">
            <div class="card h-100">
                <div class="d-flex justify-content-center py-2">
                    <i class="{{ $icon_menus['incidents'] ?? '' }} fa-4x px-2"></i>
                    <i class="{{ $icon_menus['mail'] ?? '' }} fa-4x px-2"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-center">Incidencias y Entrevistas</h5>
                    <p class="card-text">
                        Para cada incidencia registrada se podrá enviar una notificación vía correo electrónico al
                        representante del estudiante asociado,
                        allí se tiene la opción de indicar la convocatoria del mismo a una entrevista,
                        que tendrá como objetivo alcanzar acuerdos que favorezcan el bien superior del
                        niño/niña/adolecente.
                    </p>
                    <div class="my-2"> @include('bienestars.home.modals.helps.incident') </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 px-1">
            <div class="card h-100">
                <div class="d-flex justify-content-center py-2">
                    <i class="{{ $icon_menus['incident_agreements'] ?? '' }} fa-4x px-2"></i>
                    <i class="{{ $icon_menus['mail'] ?? '' }} fa-4x px-2"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-center">Acuerdo</h5>
                    <p class="card-text">
                        Una vez realizadas las entrevista y establecidos los acuerdos a que hubiera lugar, estos se
                        podrán registrar en este módulo,
                        añadiendo otro elemento de importancia al <span class="font-weight-bold">Historia Digital del
                            Estudiante</span>. Posteriormente,
                        queda habilitada la opción del envío de una notificación por correo electrónico en la cual se
                        muestren los detalles de la incidencia y de dichos acuerdos.
                    </p>
                    <div class="my-2"> @include('bienestars.home.modals.helps.agreements') </div>
                </div>
            </div>
        </div>
    </div>

<hr>

    <div class="row">

        <div class="col-lg-4 px-1">
            <div class="card h-100">
                <div class="d-flex justify-content-center py-2">
                    <i class="{{ $icon_menus['expedientDigital'] ?? '' }} fa-5x "></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-center">Historial del Estudiante</h5>
                    <p class="card-text">
                        El Historial del Estudiante contará con la recopilación de la información generada en
                        este módulo,
                        conformando un instrumento sólido y transparente, el cual se podría utilizar como elemento
                        probatorio en circunstancias que lo ameriten.
                    </p>
                    <div class="my-2"> @include('bienestars.home.modals.helps.digital_file') </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4 px-1">
            <div class="card h-100">
                <div class="d-flex justify-content-center py-2">
                    <i class="{{ $icon_menus['interviews'] ?? '' }} fa-5x "></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-center">Agenda de entrevistas</h5>
                    <p class="card-text">
                        Esta agenda muestras en dos diferentes formatos, las entrevistas programadas por docente, allí se encuentran detalles como:
                        estudiante, representante, fecha, hora, incidente, notificaciones, etc. Con el fin contar con un intrumento que de robustes y claridad a estos procesos.
                    </p>
                    <div class="my-2"> @include('bienestars.home.modals.helps.interview') </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4 px-1">
            <div class="card h-100">
                <div class="d-flex justify-content-center py-2">
                    <i class="{{ $icon_menus['tline'] ?? '' }} fa-5x "></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-center">Línea de tiempo.</h5>
                    <p class="card-text">
                        Herramienta que muestra, un formato muy didatico, los eventos que transcurren en proceso de registro y cierre de una incidencia.
                    </p>
                    <div class="my-2"> @include('bienestars.home.modals.helps.tline') </div>
                </div>
            </div>

        </div>
    </div>

</div>
