{{-- <div class="col-md-4"> --}}
    <p class="text-center">
      <strong>PLANES DE EVALUACION ASIGNADOS - EJECUTADO / PLANIFICADO</strong>
    </p>

    @component('administracion.elements.progress.bars')
        @slot('title','1er Lapso')
        @slot('actual_ammount',$real_carga_bol_1er)
        @slot('goal_ammount',$goal_carga_bol_1er)
    @endcomponent
    @component('administracion.elements.progress.bars')
        @slot('title','2do Lapso')
        @slot('actual_ammount','0')
        @slot('goal_ammount',$goal_carga_bol_1er)
    @endcomponent
    @component('administracion.elements.progress.bars')
        @slot('title','3er Lapso')
        @slot('actual_ammount','0')
        @slot('goal_ammount',$goal_carga_bol_1er)
    @endcomponent

