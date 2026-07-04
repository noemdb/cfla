{{-- <i class="bi bi-save" style="font-size: 2rem"></i> --}}
<div class="fw-bold text-dark">Plan de Evaluación/Carga de Notas</div>
<div class="text-muted small fw-bold">{{$lapso_active->name}} <small class="d-block fw-normal">Fecha de precierre ({{$lapso_active->full_date_preclosing}})</small></div>

<div class="accordion p-2" id="accordionMainProfesor">

    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Plan de Evaluación
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse bg-light" aria-labelledby="headingOne" data-bs-parent="#accordionMainProfesor" >
            <div class="accordion-body px-2">
                @include('movile.android.module.profesor.pevaluacions.partials.references')
            </div>
        </div>
    </div>

    <div class="accordion-item">
        <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Notas
            </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionMainProfesor">
            <div class="accordion-body px-2">

                @include('movile.android.module.profesor.pevaluacions.partials.notas')

            </div>
        </div>
    </div>    

</div>
