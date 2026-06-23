<!-- Modal -->
<div class="modal fade" id="modalFixCutNote" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header alert-danger">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-info p-2 border border-light rounded-pill bg-light ml-2" aria-hidden="true"></i>
                    Atención
                </h5>
            </div>
            <div class="modal-body py-2">

                <div class="card  border rounded shadow-sm border border-danger">
                    <h4 class="card-title py-1 my-1 text-center">
                        <div>
                            <div class="p-1">
                                <i class="fa fa-info fa-1x text-danger p-2 border border-danger rounded-pill" aria-hidden="true"></i>
                            </div>

                        </div>
                    </h4>
                    <div class="card-body py-1 my-1 text-center p-4 m-4">
                        <div class="text-left">
                            <h5 class="font-weight-bold">El Corte de Notas presentado para cada momento, solo incluye las notas de las evaluaciones hasta la fecha de su respectiva publicación.</h5>
                            <hr>
                            En esta sección se muestran dichas fechas.
                            @include('representants.boletins.help.lapsos')
                            <hr>
                            La totalidad de las notas están en el <span class=" font-weight-bold">Informe de Notas</span> respectivo.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@section('scripts') @parent <script type="text/javascript"> $(document).ready( function(){ $('#modalFixCutNote').modal('show'); }); </script> @endsection


