<h6 class=" font-weight-bold text-dark pb-2">
    RESUMEN
</h6>
<div class=" small font-weight-bold text-secondary pb-2">
    NOMBRE: {{ $estudiant->fullname ?? '' }}
</div>

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">

    <tbody id="tdatos">

        @foreach ($profesor_guias as $edescriptiva)

            <tr data-id="{{$edescriptiva->id}}">

                <td>
                    <div class=" d-block pl-2 text-muted">

                        <label for="lapso_id" class="font-weight-bold text-muted m-0">{{$list_comment['lapso_id'] ?? ''}}:</label>
                        <div class="form-group py-0 my-0 font-weight-light">
                            {{  (!empty($edescriptiva->lapso)) ? $edescriptiva->lapso->name : 'FINAL' }}
                        </div>

                        <div class=" pl-2">

                            <label for="observations" class="font-weight-bold text-muted m-0">{{$list_comment['observations'] ?? ''}}:</label>
                            <div class="form-group pl-1 py-0 my-0 font-weight-light">
                                {{ $edescriptiva->observations ?? '' }}
                            </div>


                            <label for="description" class="font-weight-bold text-muted m-0">{{$list_comment['description'] ?? ''}}:</label>
                            <div class="form-group pl-1 py-0 my-0 font-weight-light">
                                {{ $edescriptiva->description ?? '' }}
                            </div>

                            <label for="name" class="font-weight-bold text-muted m-0">Otros:</label>
                            <div class="form-group pl-1 py-0 my-0 font-weight-light">
                                {{ $edescriptiva->name ?? '' }}
                            </div>
                        </div>

                    </div>

                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm float-right disabled" href="#">
                        <i class="fas fa-trash"></i>
                    </a>

                    {{-- <button title="Eliminar" type="button" class="btn-create btn btn-danger btn-sm float-right">
                        <i class="fas fa-trash"></i>
                    </button> --}}

                </td>

            </tr>

        @endforeach

    </tbody>

<table>


