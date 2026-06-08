<div class="card-body p-3">
    <div class=" border rounded">

        <div class=" font-weight-bolder alert-secondary p-3">
            {{-- <i class="{{$icon_menus['pestudio'] ?? ''}}" aria-hidden="true"></i> --}}
            <div class=" float-right font-weight-normal">
                @include('administracion.home.charts.controls.area_conocimientos.legenda')
            </div>
            Promedio de notas por Área de Conocimientos
        </div>

        <div class="container-fluid">
            <div class="row">
                @foreach ($pestudios as $pestudio)
                    <div class="col-md-12 col-lg-6">
                        @include('administracion.home.charts.controls.area_conocimientos.promedio_x_area')
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
