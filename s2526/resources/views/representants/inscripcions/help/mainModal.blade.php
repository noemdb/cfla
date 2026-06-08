{{-- <div class="container small pt-1 px-3 pb-3">
    <div class="row">
        <div class="col-6">
            @include('representants.content.inscripcions.help.solvente')
            <hr>
            @include('representants.content.inscripcions.help.edit')
        </div>
        <div class="col-6">
            @include('representants.content.inscripcions.help.status')
        </div>
    </div>
</div> --}}

{{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
    Información del funcionamiento de esta sección
</button> --}}

<div class="table-secondary p-3">
    Información del funcionamiento de esta sección
    <a class="p-2 text-right" href="#" role="button"  data-toggle="modal" data-target="#inscripcionsHelp">
        <i class=" btn fa fa-info fa-1x text-info p-2 border border-info rounded-pill" aria-hidden="true"></i>
    </a>
</div>

<div class="modal fade" id="inscripcionsHelp" tabindex="-1" role="dialog" aria-labelledby="inscripcionsHelpLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header alert-secondary">
                <h5 class="modal-title text-info" id="exampleModalLabel">
                    Información del funcionamiento de esta sección
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container small pt-1 px-3 pb-3">
                    <div class="row">
                        <div class="col-6">
                            @include('representants.content.inscripcions.help.showEmail')
                            <hr>
                            {{-- @include('representants.content.inscripcions.help.edit') --}}
                        </div>
                        <div class="col-6">
                            @include('representants.content.inscripcions.help.status')
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
