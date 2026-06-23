<!-- Modal -->
<div class="modal fade" id="modalUpdateUser" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-info p-2 border border-light rounded-pill bg-light ml-2" aria-hidden="true"></i>
                    Datos de inicio de sesión
                </h5>
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> --}}
            </div>
            <div class="modal-body py-2">
                <div class="container">
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted font-weight-bold">Actualizar datos para el inición de sesión</span>
                            @include('profesors.forms.users.update')
                        </div>
                        <div class="col-6">
                            <div>
                                @include('profesors.modals.home.partials.info')
                                {{-- /home/nuser/code/s2021/resources/views/profesors/modals/home/partials/info.blade.php --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts') @parent <script type="text/javascript"> $(document).ready( function(){ $('#modalUpdateUser').modal('show'); }); </script> @endsection
