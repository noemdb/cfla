<!-- Modal -->
<div class="modal fade" id="modalUpdateUser" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-info p-2 border border-light rounded-pill bg-light ml-2" aria-hidden="true"></i>
                    Bienvenido a su primer inicio de sesión en el <span class="font-weight-bold border rounded table-success p-1" style="color:#004000">SAEFL</span>
                </h5>
            </div>
            <div class="modal-body py-2">

                <div class="container">
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted font-weight-bold">Por favor cambie sus datos de acceso</span>
                            @includeif('controls.users.form.update')
                        </div>
                        <div class="col-6">
                            <div>
                                @include('controls.home.modals.partials.info')
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@section('scripts') @parent <script type="text/javascript"> $(document).ready( function(){ $('#modalUpdateUser').modal('show'); }); </script> @endsection


