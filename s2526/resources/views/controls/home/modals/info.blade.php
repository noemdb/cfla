<!-- Modal -->
<div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
{{-- <div class="modal fade" id="modalInfo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"> --}}

    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-info p-2 border border-light rounded-pill bg-light ml-2" aria-hidden="true"></i>
                    Información importatnte
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-2">

                <div class="card  border rounded shadow-sm border border-light">
                    <h4 class="card-title py-1 my-1 text-center">
                        <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
                    </h4>
                    <div class="card-body py-1 my-1 ">
                        Su correo electrónico institucional y su contraseña de acceso inicial es:

                        <div class="p-2 text-justify text-muted">

                            <ul class="list-group">
                                <li class="list-group-item">
                                    Correo:
                                    <span class="pl-1 font-weight-bold ">{{ $user->email ?? '' }}</span>
                                </li>
                                <li class="list-group-item">
                                    Contraseña inicial:
                                    <span class="pl-1 font-weight-bold ">frayluis2020</span>
                                </li>
                            </ul>

                        </div>

                        El cual podrá ser usado para el restablecimiento de sus datos de acceso. Le recomendamos que lo guarde en un sitio seguro.

                        <hr>

                        <div>
                            Puedes ir a tu correo institucional desde el siguiente enlace:
                            <p>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" id="btnGroupAddon2">
                                            <img class="google-icon" src="{{ asset('images/avatar/social/google.svg') }}" width="24" height="24"/>
                                        </div>
                                    </div>
                                    <a name="" id="" class="form-control btn btn-light" href="https://gmail.com" role="button" target="_blank">
                                        <span class="font-weight-bold text-muted">
                                            Ingresa a tu cuenta de correo electrónico
                                        </span>
                                    </a>
                                </div>
                            </p>
                        </div>

                    </div>
                </div>

                {{--
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
                --}}
            </div>
        </div>
    </div>
</div>
@section('scripts') @parent <script type="text/javascript"> $(document).ready( function(){ $('#modalInfo').modal('show'); }); </script> @endsection
