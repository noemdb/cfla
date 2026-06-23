<nav>
    <div class="nav nav-tabs nav-fill nav-justified font-weight-bold" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-inicial-tab" data-toggle="tab" href="#nav-inicial" role="tab"
            aria-controls="nav-inicial" aria-selected="true">Inicial</a>
        <a class="nav-item nav-link" id="nav-perspectives-tab" data-toggle="tab" href="#nav-perspectives" role="tab"
            aria-controls="nav-perspectives" aria-selected="false">Perspectivas</a>
        <a class="nav-item nav-link" id="nav-evidence-tab" data-toggle="tab" href="#nav-evidence" role="tab"
            aria-controls="nav-evidence" aria-selected="false">Evidencia</a>
        <a class="nav-item nav-link" id="nav-cognitive-tab" data-toggle="tab" href="#nav-cognitive" role="tab"
            aria-controls="nav-cognitive" aria-selected="false">E.Cognitivos</a>
        <a class="nav-item nav-link" id="nav-crossCutting-tab" data-toggle="tab" href="#nav-crossCutting" role="tab"
            aria-controls="nav-crossCutting" aria-selected="false">Integración</a>
    </div>
</nav>
<div class="tab-content border border-top-0" id="nav-tabContent">

    <div class="tab-pane fade show active" id="nav-inicial" role="tabpanel" aria-labelledby="nav-inicial-tab">
        @include('livewire.profesor.debate.overlay.partials.inicial')
    </div>
    <div class="tab-pane fade" id="nav-perspectives" role="tabpanel" aria-labelledby="nav-perspectives-tab">
        @include('livewire.profesor.debate.overlay.partials.perspectives')
    </div>
    <div class="tab-pane fade" id="nav-evidence" role="tabpanel" aria-labelledby="nav-evidence-tab">
        @include('livewire.profesor.debate.overlay.partials.evidence')
    </div>
    <div class="tab-pane fade" id="nav-cognitive" role="tabpanel" aria-labelledby="nav-cognitive-tab">
        @include('livewire.profesor.debate.overlay.partials.cognitive')
    </div>
    <div class="tab-pane fade" id="nav-crossCutting" role="tabpanel" aria-labelledby="nav-crossCutting-tab">
        @include('livewire.profesor.debate.overlay.partials.crossCutting')
    </div>

</div>