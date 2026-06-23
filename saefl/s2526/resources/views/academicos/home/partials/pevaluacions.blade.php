{{-- <div class="col-md-4"> --}}
    <p class="text-center">
      <strong>PLANES DE EVALUACION ASIGNADOS - EJECUTADO / PLANIFICADO</strong>
    </p>

    @component('administracion.elements.progress.bars')
        @slot('title','1ER GRADO SECCION A LENGUA')                           
        @slot('actual_ammount','200') 
        @slot('goal_ammount','200')                                                               
    @endcomponent
    @component('administracion.elements.progress.bars')
        @slot('title','1ER GRADO SECCION B LENGUA')                           
        @slot('actual_ammount','350') 
        @slot('goal_ammount','400')                                                               
    @endcomponent
    {{-- @component('administracion.elements.progress.bars')
        @slot('title','5TO GRADO SECCION A MATEMATICA')                           
        @slot('actual_ammount','400') 
        @slot('goal_ammount','800')                                                               
    @endcomponent --}}
    @component('administracion.elements.progress.bars')
        @slot('title','5TO GRADO SECCION B MATEMATICA')                           
        @slot('actual_ammount','200') 
        @slot('goal_ammount','500')                                                               
    @endcomponent
    @component('administracion.elements.progress.bars')
        @slot('title','6TO GRADO SECCION B MATEMATICA')                           
        @slot('actual_ammount','100') 
        @slot('goal_ammount','500')                                                               
    @endcomponent

    