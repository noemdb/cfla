@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card mt-2">
            <div class="card-header alert-secondary">

                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.registro_titulos.menus.index')
                </div>

                <h4 class="pb-0 mb-0 text-uppercase">
                    <i class="fas fa-graduation-cap fa-1x"></i>
                    Gestionar <span class=" font-weight-bolder">Constancia de Promoción / Hoja de Registro Títulos</span>
                </h4>

            </div>

            <div class="card-body">

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @foreach($registro_titulos as $registro_titulo)

                            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}} font-weight-bold" id="nav-header-tab-asignacion-{{$registro_titulo->id}}" role="tab" aria-controls="nav-home" aria-selected="true"data-toggle="tab" role="tab" aria-controls="nav-home" aria-selected="true"href="#nav-content-asignacion-{{$registro_titulo->id}}" >

                                {{$registro_titulo->pestudio->name ?? ''}} <br>
                                <span class="text-muted small">
                                    || {{$registro_titulo->grado->name ?? ''}}
                                    || {{$registro_titulo->code ?? ''}}
                                    || {{$registro_titulo->tevaluacion->name ?? ''}}
                                </span>

                            </a>

                        @endforeach
                    </div>
                </nav>

                <div class="tab-content border border-top-0" id="nav-tabContent">

                    @include('administracion.elements.forms.errors')
                    @include('administracion.elements.messeges.oper_ok')

                    @foreach($registro_titulos as $registro_titulo)

                        @php
                            $grado = $registro_titulo->grado;
                            $estudiants = $grado->estudiants;
                            $pestudio = $registro_titulo->pestudio;
                        @endphp

                        <div class="tab-pane fade {{($loop->iteration==1) ? ' show active ':''}}" id="nav-content-asignacion-{{$registro_titulo->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$registro_titulo->id}}">

                            <div class="p-2">

                                <div class="row">

                                    <div class="col-10">


                                        <div class=" p-2 bd-callout bd-callout-{{$grado->color ?? 'default'}}">

                                            @includeif('administracion.registro_titulos.partials.header.'.$pestudio->code)

                                            @includeif('administracion.registro_titulos.table.'.$pestudio->code)

                                        </div>

                                    </div>

                                    <div class="col-2">

                                        <div class="float-right font-weight-bold">

                                            @includeif('administracion.registro_titulos.partials.resume.'.$pestudio->code)

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script type="text/javascript"> document.title = 'SAEFL - Registro de Título {{ (empty($estudiant->id)) ? "" : $estudiant->fullname }}'; </script>
@endsection

