<!-- Modal -->
<div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header alert-danger">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-exclamation-circle border border-light rounded-pill bg-light ml-2"></i>
                        Error inesperado - Sesión no iniciada
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
                            El sistema detectó que el correo electrónico ingresado para acceder al su cuenta Google,
                            no pertenece a algún representante o usuario SAEFL de nuestra institución, le sugerimos cerrar la sesión actual e intentar con un correo electrónico diferente.

                            <div class="d-flex justify-content-center">
                                Puedes ir a tu cuenta google desde el siguiente enlace:
                            </div>

                            <div class="">
                                <p>
                                    <div class="input-group border rounded">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="btnGroupAddon2">
                                                <img class="google-icon" src="{{ asset('images/avatar/social/google.svg') }}" width="24" height="24"/>
                                            </div>
                                        </div>
                                        <a name="" id="" class="form-control btn btn-outline-light" href="https://myaccount.google.com" role="button" target="_blank">
                                            <span class="font-weight-bold text-muted">
                                                Finalizar sesión de Google
                                            </span>
                                        </a>
                                    </div>
                                </p>
                                <hr>

                                <div>
                                    En caso de no tener el correo electrónico del representante, puedes ir a la
                                    <a href="{{url('/')}}" class=" font-weight-bold"> web principal de la institución </a>
                                    y solicitar ayuda en la sección de <b>Soporte Técnico.</b>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @section('scripts') @parent <script type="text/javascript"> $(document).ready( function(){ $('#modalError').modal('show'); }); </script> @endsection
