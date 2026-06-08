<div>

    <nav>
        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">

            <button class="nav-link p-1 active" id="nav-load-tab" data-bs-toggle="tab" data-bs-target="#nav-excecution"
                type="button" role="tab" aria-controls="nav-load" aria-selected="false">
                <i class="{{$icon_menus['evaluacion'] ?? ''}}" aria-hidden="true" style="font-size: 1rem"></i>
                <div>
                Evaluaciones    
                </div>                
            </button>

            {{-- <button class="nav-link p-1" id="nav-pevaluacion-tab" data-bs-toggle="tab" data-bs-target="#nav-pevaluacion"
                type="button" role="tab" aria-controls="nav-pevaluacion" aria-selected="false">
                <i class="{{$icon_menus['pevaluacion'] ?? ''}}" aria-hidden="true" style="font-size: 1rem"></i>
                <div>
                  P.Evaluación  
                </div>                
            </button> --}}


            <button class="nav-link p-1" id="nav-load-tab" data-bs-toggle="tab" data-bs-target="#nav-lesson"
                type="button" role="tab" aria-controls="nav-load" aria-selected="false">
                <i class="{{$icon_menus['lessons'] ?? ''}}" aria-hidden="true" style="font-size: 1rem"></i>
                <div>
                Lecciones    
                </div>                
            </button>

            {{-- 
            <button class="nav-link p-1" id="nav-papers-tab" data-bs-toggle="tab" data-bs-target="#nav-papers"
                type="button" role="tab" aria-controls="nav-papers" aria-selected="false"><i class="bi bi-bar-chart"
                    style="font-size: 1rem"></i> Indicadores</button> 
            --}}
        </div>
    </nav>

    <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">

        <div class="tab-pane fade show active" id="nav-excecution" role="tabpanel" aria-labelledby="nav-excecution-tab"
            tabindex="0">
            <livewire:movile.evaluacion.execution-component />
        </div>

        {{-- <div class="tab-pane fade" id="nav-pevaluacion" role="tabpanel" aria-labelledby="nav-pevaluacion-tab" tabindex="0">
            <livewire:movile.evaluacion.pevaluacion-component />
        </div> --}}

        <div class="tab-pane fade" id="nav-lesson" role="tabpanel" aria-labelledby="nav-lesson-tab"
            tabindex="0">
            <livewire:movile.evaluacion.lesson-component />
        </div>

        {{-- 
        <div class="tab-pane fade" id="nav-papers" role="tabpanel" aria-labelledby="nav-papers-tab" tabindex="0">
            @include('livewire.movile.evaluacion.indicators')
        </div> 
        --}}

    </div>

    

</div>