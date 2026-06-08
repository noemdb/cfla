<div class="card bd-callout bd-callout-primary">
    <h4 class="card-header py-1"> Datos </h4>
    <div class="p-2">

        <nav class=" font-weight-bolder">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-general-tab" data-toggle="tab" href="#nav-general" role="tab" aria-controls="nav-general" aria-selected="true">General</a>
                <a class="nav-item nav-link" id="nav-recaudos-tab" data-toggle="tab" href="#nav-recaudos" role="tab" aria-controls="nav-recaudos" aria-selected="false">Reacudos</a>
                <a class="nav-item nav-link" id="nav-sugerencias-tab" data-toggle="tab" href="#nav-sugerencias" role="tab" aria-controls="nav-sugerencias" aria-selected="false">Sugerencias</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                <div class=" p-2">
                    @include('representants.preinscripcions.form.fields.general')
                </div>
            </div>
            <div class="tab-pane fade" id="nav-recaudos" role="tabpanel" aria-labelledby="nav-recaudos-tab">
                <div class=" p-2">
                    @include('representants.preinscripcions.form.fields.recaudos')
                </div>
            </div>
            <div class="tab-pane fade" id="nav-sugerencias" role="tabpanel" aria-labelledby="nav-sugerencias-tab">
                <div class=" p-2">
                    @include('representants.preinscripcions.form.fields.sugerencias')
                </div>
            </div>
        </div>

    </div>
</div>
