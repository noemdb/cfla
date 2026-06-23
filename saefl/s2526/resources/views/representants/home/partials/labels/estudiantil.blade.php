<div class="card">

    <div class="card-header p-0 pl-1 alert-secondary text-uppercase font-weight-bold">
        <i class="{{$icon_menus['estudiante'] ?? ''}}" aria-hidden="true"></i>
        <b>Inscripción Estudiantíl - Planes de descuentos</b>
    </div>

    <div class="card-body p-1">

        <div class="container">
            <div class="row">
                <div class="col py-1">
                    <div class="text-center shadow-sm rounded h-100 alert-dark" style="min-height: 5rem !important">
                        <h1 class="font-weight-bolder align-middle pb-0 mb-0 text-dark">{{ ( !empty($estudiants->count()) ) ? $estudiants->count() : '' }}</h1>
                        <small class=" font-weight-bolder">
                            ESTUDIANTES INSCRITOS
                        </small>
                    </div>
                </div>
                @foreach ($pestudios as $pestudio)
                    <div class="col py-1">
                        <div class="text-center shadow-sm rounded h-100 alert-{{( !empty($pestudio->color) ) ? $pestudio->color : ''}}"  style="min-height: 5rem !important">
                            <h1 class="font-weight-bolder align-middle pb-0 mb-0 text-{{( !empty($pestudio->color) ) ? $pestudio->color : ''}}">
                                {{ ($pestudio->getInscritos($representant->id)) ? $pestudio->getInscritos($representant->id)->value : null }}
                            </h1>
                            <small class=" font-weight-bolder">
                                EN {{$pestudio->name ?? ''}}
                            </small>
                        </div>
                    </div>
                @endforeach
                @if ($plan_beneficos->count() > 0)
                    <div class="col py-1">
                        <div class="text-center shadow-sm rounded h-100 alert-success"  style="min-height: 5rem !important">
                            <h1 class="font-weight-bolder align-middle pb-0 mb-0 text-success">{{ ( !empty($plan_beneficos) ) ? $plan_beneficos->count() : ''  }}</h1>
                            <small class=" font-weight-bolder">
                                ESTUDIANTES CON DESCUENTOS
                            </small>
                        </div>
                    </div>
                @endif


                {{-- <div class="col py-3">
                    <div class="jumbotron">
                        <h1 class="display-3">Jumbo heading</h1>
                        <p class="lead">Jumbo helper text</p>
                        <hr class="my-2">
                        <p>More info</p>
                        <p class="lead">
                            <a class="btn btn-primary btn-lg" href="Jumbo action link" role="button">Jumbo action name</a>
                        </p>
                    </div>
                </div> --}}

                {{-- <div class="col py-3">
                    <div class="text-center shadow-sm rounded h-100 alert-warning">
                        <h1 class="font-weight-bolder align-middle pb-0 mb-0 text-dark">{{ ( !empty($profesors) ) ? $profesors->count() : ''  }}</h1>
                        <small class=" font-weight-bolder">
                            PROFESORES
                        </small>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>


