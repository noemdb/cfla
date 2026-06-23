<div class="card h-100">
    <div class="card-header p-0 pl-1 alert-secondary">
        <i class="{{$icon_menus['cuentas_cobrar']}}" aria-hidden="true"></i>
        <b>Estado de las Metas</b> - Ingresos
    </div>
    <div class="card-body p-1">
        <div class="container">
            <div class="row">
                @if (!empty($cuentaxpagars_last->count()))
                    <div class="col-xs p-1 m-0 w-100">
                        @component('administracion.elements.progress.bars')
                            @slot('title',$cuentaxpagars_last->first()->name)                         
                            @slot('goal_ammount',$total_meta_last)                           
                            @slot('actual_ammount',$total_pago_last)                                                     
                        @endcomponent
                    </div>
                @endif                    
                @if (!empty($cuentaxpagars_current->count()))
                    <div class="col-xs p-1 m-0 w-100">
                        @component('administracion.elements.progress.bars')
                            @slot('title',$cuentaxpagars_current->first()->name)                           
                            @slot('goal_ammount',$total_meta_current)                           
                            @slot('actual_ammount',$total_pago_current)                                                     
                        @endcomponent        
                    </div>
                @endif 
                @if (!empty($cuentaxpagars_next->count()))
                    <div class="col-xs p-1 m-0 w-100">                        
                        @component('administracion.elements.progress.bars')
                            @slot('title',$cuentaxpagars_next->first()->name)                           
                            @slot('goal_ammount',$total_meta_next)                           
                            @slot('actual_ammount',$total_pago_next)                                                     
                        @endcomponent                        
                    </div> 
                @endif                                   
            </div>
        </div>        
    </div>
</div>    