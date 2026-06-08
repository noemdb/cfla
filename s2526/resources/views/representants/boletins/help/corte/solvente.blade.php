<div class="card  border rounded shadow-sm border border-info">
    <h4 class="card-title py-1 my-1 text-center">
        <div>
            <div class="p-1">
                <i class="fa fa-info fa-1x text-info p-2 border border-info rounded-pill" aria-hidden="true"></i>
            </div>

        </div>
    </h4>
    <div class="card-body py-1 my-1 text-justify">
        En esta sección, los <b>informes</b> sólo están disponibles para los estudiantes que estén:

        <div class=" p-2 text-center">
            <span class="badge badge-success">SOLVENTES</span>
        </div>

        {{-- hasta una cuota anterior con respecto a la fecha en que se este consultando. --}}

    </div>
    <hr>

    <div class="container">
        En la siguiente ilustración se muestran como lucen los botones de acción para cada caso:
        <div class="row">
            <div class="col-6">
                <div class=" p-2 text-center">
                    <h6><span class="badge badge-success">SOLVENTES</span></h6>
                    <div>
                        <img src="{{ asset('images/example/cortes/btnSolvente.png') }}" width="92" class="img-fluid img-thumbnail" alt="">
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class=" p-2 text-center">
                    <h6><span class="badge badge-danger">INSOLVENTES</span></h6>
                    <div>
                        <img src="{{ asset('images/example/cortes/btnInSolvente.png') }}" width="92" class="img-fluid img-thumbnail p-1 m-1" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
