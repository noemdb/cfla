<div class="pb-2" x-data="{ open: false }">

    <div class="text-secondary text-center" @click="open = ! open" role="button">
        Información de esta sección
    </div>
    <div x-show="open" @click.outside="open = false">
        <div class="card card-body small">
            <div class="small text-muted pb-2">
                En esta sección, encontrarás diversas herramientas diseñadas para facilitar la planificación de actividades y la gestión de la carga de notas en cada plan de evaluación. Estas funcionalidades te permitirán realizar un seguimiento detallado del desempeño de tus estudiantes, asegurando un control preciso de su progreso académico. Además, podrás acceder a indicadores clave que te ayudarán a identificar áreas de mejora y tomar decisiones informadas, optimizando así el aprendizaje y el desarrollo de tus estudiantes.
            </div>
        </div>
    </div>

</div>

