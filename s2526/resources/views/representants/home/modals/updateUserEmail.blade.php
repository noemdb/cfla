<!-- Modal -->
<div class="modal fade" id="modalUpdateUser" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-info p-2 border border-light rounded-pill bg-light ml-2" aria-hidden="true"></i>
                    Bienvenido al <span class="font-weight-bold border rounded table-success p-1"
                        style="color:#004000">SAEFL</span>
                </h5>
            </div>
            <div class="modal-body py-2">

                <div class="container">
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted font-weight-bold">Actualice su dirección de correo
                                electrónico</span>
                            <div class="form-label-group pb-1">
                                {{-- {!! Form::text('email', ['class' => 'form-control','id'=>'email','placeholder'=>'Correo Electrónico','required']) !!} --}}
                                {{-- {!! Form::text('email', $representant->email, ['class' => 'form-control','placeholder'=>'Correo electrónico','id'=>'email','']); !!} --}}
                                {{-- <label for="email">Correo Electrónico</label> --}}

                                {{-- INI form --}}
                                {!! Form::model($user, [
                                    'route' => ['representants.users.update', $user->id],
                                    'method' => 'PUT',
                                    'id' => 'form-update-user_' . $user->id,
                                    'role' => 'form',
                                ]) !!}

                                {{-- partial con el formulario y campos --}}
                                {!! Form::text('email', $representant->email, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Correo electrónico',
                                    'id' => 'email',
                                    '',
                                ]) !!}

                                <hr>

                                <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update"
                                    data-id="update" id="btn-update-user-{{ $user->id }}">
                                    <i class="far fa-save"></i>
                                    Actualizar Correo Electrónico
                                </button>

                                {!! Form::close() !!}
                                {{-- FIN form --}}
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                @include('representants.home.modals.partials.info')
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
@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('#modalUpdateUser').modal('show');
        });
    </script>
@endsection
