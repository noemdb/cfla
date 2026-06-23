<div class="container">
    <div class="row">
        <div class="col-8">
            <span class=" font-weight-bolder">
                <span class="text-muted">
                   Nombre del reconocimiento otorgado en éste plan:
                </span>
                {{ $pestudio->title ?? null}}
            </span>
        </div>
        <div class="col-4">
            <div class="text-right">
                <div class="btn-group btn-group-sm">
                    @php $route = (!empty($registro_titulo->id)) ? route('administracion.registro_titulos.edit',$registro_titulo->id):null; @endphp
                    <a title="Editar" class="btn btn-warning btn-sm"  href="{{$route ?? null}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>
                    @php $route = (!empty($registro_titulo->id)) ? route('administracion.registro_titulos.constancia.promocion.pdf.lote',$registro_titulo->id):null; @endphp
                    <a title="Imprimir lote" class="btn btn-dark btn-sm"  href="{{$route ?? null}}" role="button" target="_blank">
                        <i class="fa fa-print" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<hr>

<span class="font-weight-bold text-secondary">Estudiantes inscritos en: <span class=" text-uppercase">{{ $grado->name ?? '' }}</span></span>
