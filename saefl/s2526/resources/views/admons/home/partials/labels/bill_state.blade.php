<div class="card h-100">
    <div class="card-header p-0 pl-1 alert-secondary">
        <i class="{{$icon_menus['pago']}}" aria-hidden="true"></i>
        <b>Deuda estudiantíl</b> - Cuentas por Cobrar
    </div>
    <div class="card-body p-1">
        <div class="container">
            <div class="row">
                @if (!empty($name_cta_last))
                    <div class="col-sm p-1 m-0">
                                               
                        @component('administracion.elements.boxes.info')
                            @slot('title',$name_cta_last)                           
                            @slot('subtitle','Estudiantes deudores')                           
                            @slot('goal_ammount',$estudiants->get()->count())                           
                            @slot('actual_ammount',$deudores_last)                                                     
                        @endcomponent                                                
                    </div>
                @endif
                @if (!empty($name_cta_current))
                    <div class="col-sm p-1 m-0">
                        
                        @component('administracion.elements.boxes.info')
                            @slot('title',$name_cta_current)                           
                            @slot('subtitle','Estudiantes deudores')                           
                            @slot('goal_ammount',$estudiants->get()->count())                           
                            @slot('actual_ammount',$deudores_current)                                                     
                        @endcomponent
                    </div>
                 @endif 
                @if (!empty($name_cta_next))
                    <div class="col-sm p-1 m-0">                                           
                        @component('administracion.elements.boxes.info')
                            @slot('title',$name_cta_next)                           
                            @slot('subtitle','Estudiantes deudores')                           
                            @slot('goal_ammount',$estudiants->get()->count())                           
                            @slot('actual_ammount',$deudores_next)                                                     
                        @endcomponent                      
                    </div> 
                @endif                   
            </div>
        </div>
    </div>
</div>    