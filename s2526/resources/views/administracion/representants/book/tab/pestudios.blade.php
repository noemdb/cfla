<hr>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Resumen</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Gráficas</a>
        {{-- <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Gráfica 2</a> --}}
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        
            <h5 class="pt-1">Planes de Estudio</h5>
            @include('administracion.inscripciones.book.table.pestudios')                        
        
    </div>
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    @include('administracion.inscripciones.chart.inscritoxgenero')                                    
                </div>
                <div class="col-md-12 col-lg-6">
                    @include('administracion.inscripciones.chart.genderxplan')                                    
                </div>                                
            </div>
        </div>                            
    </div>
</div>