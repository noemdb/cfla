<div class=" border rounded p-2">
    <nav>
        <div class="nav nav-tabs" id="nav-tab-grados" role="tablist">
            <a class="nav-item nav-link active" id="nav-resumen-tab-grados" data-toggle="tab" href="#nav-resumen-grados" role="tab" aria-controls="nav-home" aria-selected="true">Resumen</a>
            <a class="nav-item nav-link" id="nav-chart-tab-grados" data-toggle="tab" href="#nav-chart-grados" role="tab" aria-controls="nav-chart" aria-selected="false">Gráficas</a>
            {{-- <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Gráfica 2</a> --}}
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent"><div class="tab-content p-2 border border-top-0" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-resumen-grados" role="tabpanel" aria-labelledby="nav-resumen-tab-grados">
            
                <h5 class="pt-1">Grados</h5>
                @include('administracion.preinscripcions.book.table.grados')                        
            
        </div>
        <div class="tab-pane fade" id="nav-chart-grados" role="tabpanel" aria-labelledby="nav-chart-tab-grados">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        @include('administracion.preinscripcions.chart.preinscritoxgeneroxgrado')                                    
                    </div>                                               
                </div>
            </div>                            
        </div>
    </div>
</div>