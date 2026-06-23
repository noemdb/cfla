<div class="pb-2" x-data="{ open: false }">
    <div class="text-secondary text-center" @click="open = ! open" role="button">
        <i class="bi bi-file-text" style="font-size: 1rem"></i> {{$profesor->fullname ?? ''}}
    </div>
    <div x-show="open" @click.outside="open = false">
        <div class="card card-body small">
            <div class="small text-muted pb-2">
                @include('movile.android.module.profesor.card')
            </div>
        </div>
    </div>
</div>

<nav>
    <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
        
        {{-- <button class="nav-link p-1 active" id="nav-papers-tab" data-bs-toggle="tab" data-bs-target="#nav-papers" type="button" role="tab" aria-controls="nav-papers" aria-selected="false"><i class="bi bi-bar-chart" style="font-size: 1rem"></i> Indicadores</button> --}}
        <button class="nav-link p-1 active" id="nav-papers-tab" data-bs-toggle="tab" data-bs-target="#nav-papers" type="button" role="tab" aria-controls="nav-papers" aria-selected="false"><i class="bi bi-bar-chart" style="font-size: 1.2rem"></i></button>
        
        {{-- <button class="nav-link p-1" id="nav-activity-tab" data-bs-toggle="tab" data-bs-target="#nav-activity" type="button" role="tab" aria-controls="nav-activity" aria-selected="false"><i class="fas fa-book" style="font-size: 1rem"></i> Actividades</button> --}}
        <button class="nav-link p-1" id="nav-activity-tab" data-bs-toggle="tab" data-bs-target="#nav-activity" type="button" role="tab" aria-controls="nav-activity" aria-selected="false"><i class="fas fa-book" style="font-size: 1.2rem"></i></button>
        
        {{-- <button class="nav-link p-1" id="nav-lesson-tab" data-bs-toggle="tab" data-bs-target="#nav-lesson" type="button" role="tab" aria-controls="nav-load" aria-selected="false"><i class="fas fa-book-reader" style="font-size: 1rem"></i> Lecciones</button> --}}
        <button class="nav-link p-1" id="nav-lesson-tab" data-bs-toggle="tab" data-bs-target="#nav-lesson" type="button" role="tab" aria-controls="nav-load" aria-selected="false"><i class="fas fa-book-reader" style="font-size: 1.2rem"></i></button>
        
        {{-- <button class="nav-link p-1" id="nav-load-tab" data-bs-toggle="tab" data-bs-target="#nav-load" type="button" role="tab" aria-controls="nav-load" aria-selected="false"><i class="bi bi-box-arrow-down" style="font-size: 1rem"></i> Cargas</button> --}}
        <button class="nav-link p-1" id="nav-load-tab" data-bs-toggle="tab" data-bs-target="#nav-load" type="button" role="tab" aria-controls="nav-load" aria-selected="false"><i class="bi bi-box-arrow-down" style="font-size: 1.2rem"></i></button>
        
        {{-- <button class="nav-link p-1" id="nav-incidents-tab" data-bs-toggle="tab" data-bs-target="#nav-incidents" type="button" role="tab" aria-controls="nav-incidents" aria-selected="false"><i class="bi bi-file-medical" style="font-size: 1rem"></i> Incidencias</button> --}}
    </div>
</nav>

<div class="tab-content border border-top-0 bg-white" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-papers" role="tabpanel" aria-labelledby="nav-papers-tab" tabindex="0">
        @include('movile.android.module.profesor.indicators.main')
    </div>

    <div class="tab-pane fade" id="nav-activity" role="tabpanel" aria-labelledby="nav-activity-tab" tabindex="0">       
        @include('movile.android.module.profesor.activities.main')
    </div>
    
    <div class="tab-pane fade" id="nav-lesson" role="tabpanel" aria-labelledby="nav-lesson-tab" tabindex="0">
        @php $isLesson = $profesor->isLesson @endphp
        @if ($isLesson)
            @include('movile.android.module.profesor.lessons.main')
        @else
            <div class="alert alert-warning p-2 m-2" role="alert" >
                <div class="text-center fw-bold">Prueba piloto</div>
                <div class="fw-light">Control y seguimiento de las lecciones de los docentes</div>
            </div>            
        @endif                
    </div>
    
    <div class="tab-pane fade" id="nav-load" role="tabpanel" aria-labelledby="nav-load-tab" tabindex="0">
        @include('movile.android.module.profesor.pevaluacions.main')
    </div>
    {{-- 
    <div class="tab-pane fade" id="nav-incidents" role="tabpanel" aria-labelledby="nav-incidents-tab" tabindex="0"> <i class="bi bi-book"></i>
        @include('movile.android.module.profesor.incidents.main')
    </div>
    --}}
</div>

