<div class="container-fluid">
    <div class="row">

        <div class="col-sm-4 col-md-3 py-2">
            @component('plannings.elements.boxes.indicators')
                @slot('title','Total de actividades planificadas')
                @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                @slot('total', $activities->count() )
                @slot('icon',$icon_menus['activities'])
            @endcomponent
        </div>

        <div class="col-sm-4 col-md-3 py-2">
            @php $avgActivitiesPerPlan = $pestudio->getAvgActivitiesPerPlan($lapso->id); @endphp
            @component('plannings.elements.boxes.indicators')
                @slot('title','Indicador de Cobertura Curricular')
                @slot('subtitle','Promedio de actividades por Área de Formación')
                @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                @slot('total', f_float($avgActivitiesPerPlan) )
                @slot('icon',$icon_menus['pevaluacion'])
            @endcomponent
        </div>

        <div class="col-sm-4 col-md-3 py-2">
            @php 
                $activeTeachersCount = $pestudio->getActiveTeachersCount($lapso->id); 
                $teachersCount = $pestudio->getTeachersCount($lapso->id); 
                $title = 'Cantidad de profesores activos';
                $subtitle = 'de '.$teachersCount.' con carga académica';
            @endphp
            @component('plannings.elements.boxes.indicators')
                @slot('title',$title)
                @slot('subtitle',$subtitle)
                @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                @slot('total', $activeTeachersCount )
                @slot('icon',$icon_menus['profesor'])
            @endcomponent
        </div>

        <div class="col-sm-4 col-md-3 py-2">
            @php 
                // $teachersCount = $pestudio->getTeachersCount($lapso->id); 
                $indice = ($teachersCount <> 0) ? round(100 * $activeTeachersCount / $teachersCount,1) : null; 
                $title = 'Indicador de Participación';
                $subtitle = 'Porcentaje de Docentes con Planificaciones Activas';
            @endphp
            @component('plannings.elements.boxes.indicators')
                @slot('title',$title)
                @slot('subtitle',$subtitle)
                @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                @slot('total', $indice.'%' )
                @slot('icon',$icon_menus['chartpie'])
            @endcomponent
        </div>

        <div class="col-sm-4 col-md-3 py-2">
            @php 
                $activitiesCount = $activities->count(); 
                $activitiesComentCount = $activities->where('comments','<>',null)->count(); 
                $indice = ($activitiesCount <> 0) ? round(100 * $activitiesComentCount / $activitiesCount,1) : null; 
                $title = 'Indicador de Seguimiento';
                $subtitle = 'Tasa de Comentarios en Actividades';
            @endphp
            @component('plannings.elements.boxes.indicators')
                @slot('title',$title)
                @slot('subtitle',$subtitle)
                @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                @slot('total', $indice.'%' )
                @slot('icon',$icon_menus['messege'])
            @endcomponent
        </div>

        <div class="col-sm-4 col-md-3 py-2">
            @php 
                $activitiesCount = $activities->count(); 
                $activitiesActiveCount = $activities->where('status',true)->count(); 
                $indice = ($activitiesCount <> 0) ? round(100 * $activitiesActiveCount / $activitiesCount,1) : null; 
                $title = 'Indicador de Aprobación';
                $subtitle = 'Porcentaje de Actividades Aprobadas';
            @endphp
            @component('plannings.elements.boxes.indicators')
                @slot('title',$title)
                @slot('subtitle',$subtitle)
                @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                @slot('total', $indice.'%' )
                @slot('icon',$icon_menus['evaluacion'])
            @endcomponent
        </div>        

        <div class="col-sm-4 col-md-3 py-2">
            @php 
                $pevaluacionsCount = $pestudio->getPevaluacions($lapso->id)->count(); 
                $pevaluacionsObsCount = $pestudio->getPevaluacions($lapso->id)->where('observations','<>',null)->count(); 
                $indice = ($pevaluacionsCount <> 0) ? round(100 * $pevaluacionsObsCount / $pevaluacionsCount,1) : null; 
                $title = 'Indicador de Supervisión';
                $subtitle = 'Tasa de Observaciones en A.Formación';
            @endphp
            @component('plannings.elements.boxes.indicators')
                @slot('title',$title)
                @slot('subtitle',$subtitle)
                @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                @slot('total', $indice.'%' )
                @slot('icon',$icon_menus['messege'])
            @endcomponent
        </div>
        
    </div>
</div>