@php
    // $finicial = $lapso_active->finicial;
    $finicial = $lapso_active->academic_start_date;
    $ffinal = $lapso_active->ffinal;
    $weeks = getWeeksInPeriod($finicial,$ffinal);
@endphp

<div class="card">
    <div class="card-body">
        <small class="text-muted font-weight-light">Momento de evalaución actual</small>
        <h5 class="card-title mb-0 font-weight-bold">{{$lapso_active->name}}</h5>        
        <small class="text-muted small font-weight-bold">{{f_date($finicial)}} hasta {{f_date($ffinal)}}</small>
        <div class="mt-1">
            <div class="h6 font-weight-bold mb-0 pb-0">Porcentaje de Áreas de Formación con Registro de Actividades Completas por Semana</div>            
            <div>
                <strong>Definición:</strong>
                Este indicador mide el porcentaje de áreas de formación que han registrado al menos una actividad completa (Enseñanza/Aprendizaje) en una semana específica. Una actividad completa se define como aquella que incluye información tanto de enseñanza como de aprendizaje, asegurando que los registros cumplan con los requisitos pedagógicos establecidos.
            </div>
            <div>
                <strong>Interpretación</strong>
                Un valor alto del indicador sugiere un nivel óptimo de cumplimiento en el registro de actividades que cumplen con los estándares de planificación pedagógica. Un valor bajo puede indicar problemas en el diseño o seguimiento de las actividades de enseñanza y aprendizaje.
            </div>
        </div>
        <nav class="mt-2">
            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                @foreach($weeks as $week)
                    <a class="nav-item nav-link  {{($loop->iteration == 1) ? 'active':''}}" id="nav-header-tab-week-{{$week['start']}}" data-toggle="tab" href="#nav-content-week-{{$week['start']}}" role="tab" aria-controls="nav-home" aria-selected="true">
                        SEM {{$loop->iteration}}                        
                    </a>
                @endforeach
            </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
            @foreach($weeks as $week)
                <div class="tab-pane fade {{($loop->iteration == 1) ? 'show active':''}}" id="nav-content-week-{{$week['start']}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$week['start']}}">
                    <div class="border border-top-0 rounded-bottom pb-2 p-2">

                        
                        <div class="p-2">

                            <div class="text-muted font-weight-bold">Desde el {{f_date($week['start'])}} hasta el {{f_date($week['end'])}}</div>
                            
                            @foreach($peducativos as $peducativo)

                                @php
                                    $pevaluacions_goal = $peducativo->getPevaluacions($lapso_active->id);
                                    $start = $week['start'];
                                    $end = $week['end'];
                                    $pevaluacions_real = $peducativo->getPevaluacionsWithActivitiesComplete($lapso_active->id,$start,$end);
                                    $goal = (! $pevaluacions_goal->isEmpty() ) ? $pevaluacions_goal->count() : null;
                                    $real = (! $pevaluacions_real->isEmpty() ) ? $pevaluacions_real->count() : null;
                                @endphp
        
                                @component('administracion.elements.progress.bars')
                                    @slot('title',$peducativo->name)                           
                                    @slot('goal_ammount',$goal)                                                               
                                    @slot('actual_ammount',$real) 
                                @endcomponent
    
                            @endforeach

                        </div> 
                        
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

