<nav>
    <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
        <button class="nav-link p-1 active" id="nav-details-tab" data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab" aria-controls="nav-details" aria-selected="true">Consultas activas</button>
        <button class="nav-link p-1 " id="nav-papers-tab" data-bs-toggle="tab" data-bs-target="#nav-papers" type="button" role="tab" aria-controls="nav-papers" aria-selected="false">Otras consulta</button>
    </div>
</nav>
<div class="tab-content border border-top-0 bg-white" id="nav-tabContent">

    <div class="tab-pane fade p-2 show active" id="nav-details" role="tabpanel" aria-labelledby="nav-details-tab" tabindex="0">
        <div class="p-1">
            <livewire:poll.index-component />
        </div>
    </div>

    <div class="tab-pane fade p-2" id="nav-papers" role="tabpanel" aria-labelledby="nav-papers-tab" tabindex="0">
        <div class="p-1 h-100">
            No hay participaciones en procesos de consultas anteriores.
        </div>
    </div>

</div>
