
<h4 class=" alert pb-0 mb-0 border-top mt-"> Administración </h4>
<div class="row">
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title pb-0 mb-0">
                    Representantes
                </h4>
                <span class="text-muted small ">[Representantes]</span>
                <p class="card-text ">
                    <a href="{{ route('administracion.representants.list.full.dw.excel') }}" class="btn btn-primary" target="_blank">
                        <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                        Descargar XLS
                    </a>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title pb-0 mb-0">
                    Estudiantes
                </h4>
                <span class="text-muted small ">[Datos]</span>
                <p class="card-text ">
                    <a href="{{ route('administracion.estudiants.list.full.dw.excel') }}" class="btn btn-info" target="_blank">
                        <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                        Descargar XLS
                    </a>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title pb-0 mb-0">
                    Ins. Administrativas
                </h4>
                <span class="text-muted small ">[Estudiantes]</span>
                <p class="card-text ">
                    <a href="{{ route('administracion.administrativas.list.dw.excel.get') }}" class="btn btn-dark" target="_blank">
                        <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                        Descargar XLS
                    </a>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title pb-0 mb-0">
                    Sados
                </h4>
                <span class="text-muted small ">[Estudiantes]</span>
                <p class="card-text ">
                    <a href="{{ route('administracion.estudiants.list.saldos.dw.excel') }}" class="btn btn-danger" target="_blank">
                        <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                        Descargar XLS
                    </a>
                </p>
            </div>
        </div>
    </div>

</div>

<hr>

<div class="row">
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title pb-0 mb-0">
                    Pagos
                </h4>
                <span class="text-muted small ">[Estudiantes]</span>
                <p class="card-text ">
                    <a href="{{ route('administracion.registropagos.export.xls') }}" class="btn btn-success" target="_blank">
                        <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                        Descargar XLS
                    </a>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title pb-0 mb-0">
                    Abonos
                </h4>
                <span class="text-muted small ">[Representantes]</span>
                <p class="card-text ">
                    <a href="{{ route('administracion.abonos.list.abono.dw.excel') }}" class="btn btn-light" target="_blank">
                        <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                        Descargar XLS
                    </a>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title pb-0 mb-0">
                    Creditos a Favor
                </h4>
                <span class="text-muted small ">[Representantes]</span>
                <p class="card-text ">
                    <a href="{{ route('administracion.creditoafavors.list.credito.dw.excel') }}" class="btn btn-secondary" target="_blank">
                        <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                        Descargar XLS
                    </a>
                </p>
            </div>
        </div>
    </div>
    {{-- <div class="col-sm-3">
        <div class="card">
            <div class="card-body">
            <h3 class="card-title">Title</h3>
            <p class="card-text">Text</p>
            </div>
        </div>
    </div> --}}
</div>
