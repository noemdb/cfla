<div class="pb-2" x-data="{ open: false }">

    <div class="text-secondary text-center" @click="open = ! open" role="button">
        Información de esta sección
    </div>
    <div x-show="open" @click.outside="open = false">
        <div class="card card-body small">
            <div class="small text-muted pb-2">
                En esta sección se tiene una herramienta de consulta, en la que usted puede observar información de interés, deuda, cuotas pendientes, estudiantes inscritos, etc.
            </div>
        </div>
    </div>

</div>

