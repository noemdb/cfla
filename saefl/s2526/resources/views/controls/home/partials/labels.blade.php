{{-- INI dashboard widget --}}
{{-- <div class="container p-1"> --}}
    {{-- INI labels --}}
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6">
            {{-- INI card-progress goals cuentas de cobro --}}
            <div class="card">
                <div class="card-header p-0 pl-1 alert-secondary">
                    <b>Estado de las Metas</b> - Cuentas de Cobro
                </div>
                <div class="card-body p-1">
                    @if (!empty($cuentaxpagars_last->count()))
                        @component('administracion.elements.progress.bars')
                        @slot('title',$cuentaxpagars_last->first()->name)                         
                            @slot('goal_ammount',$total_meta_last)                           
                            @slot('actual_ammount',$total_pago_last)                                                     
                        @endcomponent
                    @endif
                    @if (!empty($cuentaxpagars_current->count()))
                        @component('administracion.elements.progress.bars')
                            @slot('title',$name_cta_current)
                            @slot('title',$cuentaxpagars_current->first()->name)                           
                            @slot('goal_ammount',$total_meta_current)                           
                            @slot('actual_ammount',$total_pago_current)                                                     
                        @endcomponent
                    @endif
                    @if (!empty($cuentaxpagars_next->count()))
                        @component('administracion.elements.progress.bars')
                            @slot('title',$cuentaxpagars_next->first()->name)                           
                            @slot('goal_ammount',$total_meta_next)                           
                            @slot('actual_ammount',$total_pago_next)                                                     
                        @endcomponent
                    @endif
                </div>
            </div>            
            {{-- INI card-collapse tasks --}}
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            {{-- @admin --}}
            <div class="container">
                <div class="row">
                    <div class="col-xs-6">
                        @if (!empty($cuentaxpagars_last->count()) )
                            @php $total = $registro_pago_last->count() / $estudiants * 100; @endphp
                            <div class="info-box">
                                <span class="info-box-icon bg-aqua">
                                {{-- <i class="fas fa-archive" aria-hidden="true"></i> --}}
                                {{round($total)}}<span class="small">%</span>
                                </span>        
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        <b>{{$cuentaxpagars_last->first()->name ?? ''}}</b>
                                    </span><br>
                                    <span class="info-box-text">Pagos Registrado</span>
                                    {{-- <span class="info-box-number">{{round($total)}}<small>%</small></span> --}}
                                </div>
                            </div>
                        @endif                        
                    </div>
                    <div class="col-xs-6">
                        @if (!empty($cuentaxpagars_next->count()))
                            @php $total = $registro_pago_next->count() / $estudiants * 100; @endphp
                            <div class="info-box">
                                <span class="info-box-icon bg-aqua">
                                    {{-- <i class="fas fa-archive" aria-hidden="true"></i> --}}
                                    {{round($total)}}<span class="small">%</span>
                                </span>
                    
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        <b>{{$cuentaxpagars_next->first()->name ?? ''}}</b>
                                    </span><br>
                                    <span class="info-box-text">Pagos Registrado</span>
                                    {{-- <span class="info-box-number">{{round($total)}}<small>%</small></span> --}}
                                </div>
                            </div>
                          @endif
                    </div>                    
                </div>
            </div>
            {{-- @endadmin           --}}
           
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 p-1">
            {{-- INI card-collapse tasks --}}
            {{-- @component('elements.widgets.label')
                @slot('class', 'info')
                @slot('iconTitle', $icon_menus['task'].' fa-3x')
                @slot('total', $tasks->where('estado','INICIADA')->count())
                @slot('title', 'Tareas Pendientes')
                @slot('subtitle', 'Últimas 5')
                @slot('headercollapse', 'Mas detalles')
                @slot('id', 'idtareas_label')
                @slot('panelControls', true)
                @slot('body')
                    @include('elements.widgets.tasks.list',[
                        'tasks'=>$tasks->where('estado','INICIADA')->take(5),
                        'tasks'=>$tasks,
                        'show_task'=>'true'
                        ])
                @endslot
            @endcomponent --}}
            {{-- INI card-collapse tasks --}}
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 p-1">
            {{-- INI card-collapse alerts --}}
            {{-- @component('elements.widgets.label')
                @slot('class', 'danger')
                @slot('iconTitle', $icon_menus['alert'].' fa-3x')
                @slot('total', $alerts->where('estado','Enviada')->count())
                @slot('title', 'Alertas Enviadas')
                @slot('subtitle', 'Últimas 5')
                @slot('headercollapse', 'Mas detalles')
                @slot('id', 'idalerts_label')
                @slot('panelControls', true)
                @slot('body')
                    @include('elements.widgets.alerts.list',[
                        'alerts'=>$alerts,
                        'alerts'=>$alerts->where('estado','Enviada')->take(5),
                        'show_alert'=>'true'
                        ])
                @endslot
            @endcomponent --}}
            {{-- INI card-collapse alerts --}}
        </div>

    </div>
    {{-- FIN labels --}}
{{-- </div> --}}
{{-- FIN dashboard widget --}}