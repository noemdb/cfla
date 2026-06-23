<div class="card h-100">
    <div class="card-header p-0 pl-1 alert-secondary">
        <i class="{{$icon_menus['pago']}}" aria-hidden="true"></i>
        <b>Estudiantes Deudores</b> por Grado
    </div>
    <div class="card-body p-1">
        <div class="container">
            <div class="row">
                @if (!empty($name_cta_last))
                    <div class="col-sm p-1 m-0">
                        @php 
                            $grado_last = $grado->count_estudiants($deudores_grado_last->grado_id);
                            $goal_ammount = $grado_last->count();
                            $actual_ammount = $deudores_grado_last->count;
                        @endphp 
                        @component('administracion.elements.boxes.info')
                            @slot('title',$name_cta_last)                           
                            @slot('subtitle',$deudores_grado_last->name)                                                   
                            @slot('goal_ammount',$goal_ammount))                           
                            @slot('actual_ammount',$actual_ammount)                                                     
                        @endcomponent                                                
                    </div>
                @endif
                @if (!empty($name_cta_current))
                    <div class="col-sm p-1 m-0">
                        @php 
                            $grado_current = $grado->getCountEstudiants($deudores_grado_current->grado_id);
                            $goal_ammount = $grado_current->count();
                            $actual_ammount = $deudores_grado_current->count;
                        @endphp 
                        @component('administracion.elements.boxes.info')
                            @slot('title',$name_cta_current)                            
                            @slot('subtitle',$deudores_grado_current->name)                            
                            @slot('goal_ammount',$goal_ammount))                           
                            @slot('actual_ammount',$actual_ammount)                                                    
                        @endcomponent
                    </div>
                 @endif 
                @if (!empty($name_cta_next ?? 'fallo'))
                    <div class="col-sm p-1 m-0">
                        @php 
                            $grado_next = $grado->getCountEstudiants($deudores_grado_next->grado_id);
                            $goal_ammount = $grado_next->count();
                            $actual_ammount = $deudores_grado_next->count;
                        @endphp
                        @component('administracion.elements.boxes.info')
                            @slot('title',$name_cta_next)                         
                            @slot('subtitle',$deudores_grado_next->name)
                            @slot('goal_ammount',$goal_ammount))                           
                            @slot('actual_ammount',$actual_ammount)
                        @endcomponent                      
                    </div> 
                @endif                   
            </div>
        </div>
    </div>
</div>    