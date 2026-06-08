@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary py-1">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.pensums.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Configurar <span class="font-weight-bolder">Pensums</span> para los grados de los Planes de Estudio</h4>
            </div>

            <div class="card-body small">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok')

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @foreach($pestudios as $pestudio)
                            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" id="nav-header-tab-{{$pestudio->id}}" data-toggle="tab" href="#nav-content-{{$pestudio->id}}" role="tab" aria-controls="nav-home" aria-selected="true">{{ $pestudio->fullname ?? '' }}</a>
                        @endforeach
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    @foreach($pestudios as $pestudio)
                        <div class="tab-pane fade {{($loop->iteration==1) ? 'show active':''}} border border-top-0  p-2" id="nav-content-{{$pestudio->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$pestudio->id}}">
                            <div class="card">
                                <div class="card-header p-1 alert-secondary font-weight-bold">
                                    <h5><span class="font-weight-bold p-1">Pensum</span></h5>
                                </div>
                                <div class="card-body px-3 py-2">
                                    @php $grados = $pestudio->getGradosActive() @endphp
                                    @foreach ($grados as $grado)
                                        <ul class="list-group">
                                            <li class="list-group-item bd-callout bd-callout-{{$grado->color ?? 'default'}} p-1 ">

                                                <span class="float-right">
                                                    @php $id_modal = 'id_modal'.$grado->id; @endphp
                                                    @include('administracion.configuraciones.pensums.modal.create')
                                                </span>                                               

                                                <span class=" font-weight-bold text-uppercase">{{$grado->name}}</span>

                                            </li>
                                            <li class="list-group-item p-1 small border-top-0">
                                                @php $pensums = (!empty($grado->pensums)) ? $grado->pensums : null; @endphp
                                                @includewhen((count($pensums)>0),'administracion.configuraciones.pensums.partials.asignaturas')
                                            </li>
                                        </ul>
                                        <hr class="pb-2">
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>

            </div>
        </div>
    </main>
@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Pensums, Listado'; </script> @endsection

@section('scripts')
    @parent
    <script>
        $('.btn-print').click(function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            var w = 820, h = 440; // default sizes
            if (window.screen) {
                // w = window.screen.availWidth * percent / 100;
                h = window.screen.availHeight;
            }
            windowObjectReference = window.open(
                url,
                "CERTIFICACIÓN DE NOTAS",
                "resizable,scrollbars,status,width=800,height="+h
            );
        });
    </script>
@endsection