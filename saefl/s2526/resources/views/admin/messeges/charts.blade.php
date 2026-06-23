@extends('admin.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        {{-- ini tab --}}
        <nav class="pt-1 mt-1">
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-tab01-tab" data-toggle="tab" href="#nav-tab01" role="tab" aria-controls="nav-tab01" aria-selected="true">Gráficas 1</a>
            <a class="nav-item nav-link" id="nav-tab02-tab" data-toggle="tab" href="#nav-tab02" role="tab" aria-controls="nav-tab02" aria-selected="false">Gráficas 2</a>
          </div>
        </nav>
        
        <div class="tab-content" id="nav-tabContent">
          {{-- ini tab01 --}}
          <div class="tab-pane fade show active" id="nav-tab01" role="tabpanel" aria-labelledby="nav-tab01-tab">
              
              <div class="row">
                
                <div class="col-lg-6 col-md-6 col-sm-12">

                    @include('admin.messeges.chart.messegesmonthchart')
                  
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">

                  @include('admin.messeges.chart.messegesusers')                    

                </div>

              </div>   
                 
          </div>
          {{-- fin tab01 --}}

          {{-- ini tab02 --}}
          <div class="tab-pane fade" id="nav-tab02" role="tabpanel" aria-labelledby="nav-tab02-tab">

              <div class="row">
                
                <div class="col-lg-6 col-md-6 col-sm-12">

                  @include('admin.messeges.chart.messegestypes')
                  
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12">

                    {{-- @include('admin.messeges.chart.rolsrangeschart') --}}

                </div>

              </div>

          </div>
          {{-- fin tab02 --}}
        </div>
        {{-- fin tab --}}

    </main>

@endsection

@section('scripts')
    @parent
    {{-- <script src="{{ asset("js/Chart.js") }}"></script> --}}
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    {{-- <script src="{{ asset("js/utils.js") }}"></script> --}}
    <script src="{{ asset("js/ChartFunction.js") }}"></script>{{-- Funciones para generar los Chart --}}

    {{-- INI Evento clic para generar los Chart por rango--}}
    <script src="{{ asset("js/ChartEvent.js") }}"></script>{{-- Funciones para generar los Chart --}}
    {{-- FIN Evento clic para generar los Chart por rango --}}

@endsection