{{-- <div class=" p-2 m-2 border rounded"> --}}

    <h4 class=" alert pb-0 mb-0 border-top mt-2"> Coordinación Control de Estudios </h4>

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
                        Ins. Académiscas
                    </h4>
                    <span class="text-muted small ">[Estudiantes]</span>
                    <p class="card-text ">
                        <a href="{{ route('administracion.inscripcions.list.dw.excel') }}" class="btn btn-dark" target="_blank">
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
                        Profesores
                    </h4>
                    <span class="text-muted small ">[Profesores]</span>
                    <p class="card-text ">
                        <a href="{{ route('administracion.profesors.list.dw.excel') }}" class="btn btn-danger" target="_blank">
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
                        Carga Académica
                    </h4>
                    <span class="text-muted small ">[Profesores]</span>
                    <p class="card-text ">
                        <a href="{{ route('administracion.pevaluacions.list.dw.excel') }}" class="btn btn-success" target="_blank">
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
                        Evaluaciones
                    </h4>
                    <span class="text-muted small ">[Profesores]</span>
                    <p class="card-text ">
                        <a href="{{ route('administracion.evaluacions.list.dw.excel') }}" class="btn btn-light" target="_blank">
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
                        Notas
                    </h4>
                    <span class="text-muted small ">[Profesores]</span>
                    <div class="card-text border rounded">
                        {{-- <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i> --}}
                        {{-- Descargar XLS --}}
                        @foreach ($lapsos as $lapso)
                        <div>Momento {{$lapso->id ?? ''}}</div>
                        {{-- <div class="snmall">Grados/Niveles</div> --}}
                            {{-- <div class="btn-group btn-group-sm" role="group" aria-label="Basic example"> --}}
                                @foreach ($grados as $grado)
                                    <a href="{{ route('administracion.boletins.list.dw.excel',["lapso_id"=>$lapso->id,"grado_id"=>$grado->id]) }}"
                                        {{-- class="btn btn-link btn-sm {{$grado->ClassTextColor ?? ''}}" --}}
                                        target="_blank">
                                        {{-- {{$grado->id ?? ''}} --}}
                                        <span class="badge badge-light border rounded {{$grado->ClassTextColor ?? ''}}">{{$grado->id ?? ''}}</span>
                                    </a>
                                @endforeach
                            {{-- </div> --}}
                        @endforeach

                        {{-- <a href="{{ route('administracion.boletins.list.dw.excel') }}" class="btn btn-secondary" target="_blank">
                            <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                            Descargar XLS
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="card-title pb-0 mb-0">
                        Ajuste de Notas
                    </h4>
                    <span class="text-muted small ">[Profesores]</span>
                    <p class="card-text ">
                        <a href="{{ route('administracion.boletin_ajustes.list.dw.excel') }}" class="btn btn-warning" target="_blank">
                            <i class="{{$icon_menus['xls'] ?? ''}}" aria-hidden="true"></i>
                            Descargar XLS
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

{{-- </div> --}}
