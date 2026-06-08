<nav id="nav-tab-{{$estudiant->id}}">
    <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home-{{$estudiant->id}}" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
            Notas
        </button>
        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile-{{$estudiant->id}}" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
            Corte
        </button>
    </div>
</nav>
<div class="tab-content border border-top-0 bg-white" id="nav-tabContent">
    <div class="tab-pane fade show active p-2" id="nav-home-{{$estudiant->id}}" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
        @include('movile.android.module.representant.estudiants.partials.notas')
    </div>
    <div class="tab-pane fade p-2" id="nav-profile-{{$estudiant->id}}" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
        @include('movile.android.module.representant.estudiants.partials.corte')
    </div>
</div>

<div class="d-flex border rounded mt-2">
    <div><i class="bi bi-info text-info fw-bold" style="font-size: 4rem"></i></div>
    <div class="text-muted small"> Para poder descargar los informes, es necesario haber cancelado las cuotas correspondientes al servicio de escolaridad y que se hayan cumplido las fechas de finalización en cada momento de evaluación. </div>
</div>


