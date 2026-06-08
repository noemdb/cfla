@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        @include('administracion.elements.forms.errors')
        @include('administracion.elements.messeges.oper_ok')

        <div class="card card-primary mt-2">

            <div class="card-header alert-secondary">
                <div class="btn-group float-right pt-2">
                    @include('administracion.configuraciones.profesor_guias.menus.index')
                </div>

                <h4><span class="font-weight-bolder">Profesores Guía</span> registrados</h4>
            </div>

            <div class="card-body p-1 m-1">
                
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @foreach($lapsos as $lapso)
                            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}} font-weight-bold text-uppercase small"
                                id="nav-header-tab-gprofesors-{{$lapso->id}}" data-toggle="tab"
                                href="#nav-content-gprofesors-{{$lapso->id}}" role="tab" aria-controls="nav-home" aria-selected="true">
                                {{$lapso->name ?? ''}}
                            </a>
                        @endforeach
                    </div>
                </nav>
                
                <div class="tab-content border border-top-0" id="nav-tabContent">                    
                    @foreach ($lapsos as $lapso)
                        @php $datas = $profesor_guias->where('lapso_id',$lapso->id)->SortBy('grado_id'); @endphp
                        <div class="tab-pane fade {{($loop->iteration==1) ? 'show active':''}}" id="nav-content-gprofesors-{{$lapso->id ?? ''}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$lapso->id ?? ''}}">
                            @if (!empty($datas->count())) 
                                <div class="d-block p-1">
                                    @include('administracion.configuraciones.profesor_guias.deck.gprofesors')                           
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </main>

@endsection
