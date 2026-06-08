@php $arr = ['primary','secondary','success','info','warning','danger','dark'] @endphp
<div class="container-fluid">

    <div class="row">
        <div class="col-sm-6">

            @include('administracion.matriculations.catchments.indicators.partials.gradoIdTotals') 

            {{-- <hr class="my-1 py-1"> --}}
            <br>

            @include('administracion.matriculations.catchments.indicators.partials.pestudioIdTotals')  
            
            <br>

            @include('administracion.matriculations.catchments.indicators.partials.totalRegister') 

            <br>

            @include('administracion.matriculations.catchments.indicators.partials.institutionOriginTotalsForInitial') 
            
        </div>

        <div class="col-sm-6">

            @include('administracion.matriculations.catchments.indicators.partials.institutionOriginTotalsForGrade') 
            
            <br>

            @include('administracion.matriculations.catchments.indicators.partials.dailyHourlyTotals') 
            
        </div>
        
    </div>

    <hr>
    
    <div class="row">
        
        <div class="col-sm-6">
                       
            @include('administracion.matriculations.catchments.indicators.partials.institutionOriginTotals')    
                        
        </div>

        <div class="col-sm-6">

            @include('administracion.matriculations.catchments.indicators.partials.groupIdTotals')        
            
        </div>
        
    </div>

    <hr>

    <div class="row">

        <div class="col-6">

            

        </div>

        <div class="col-6">

        </div>
    </div>
    
</div>

  

