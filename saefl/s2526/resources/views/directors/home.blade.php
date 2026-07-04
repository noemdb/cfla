@extends('directors.layouts.dashboard.app')

@section('main')

    <main role="main">

        {{-- <div class="container pt-1 w-100"> --}}
            <div class="row">

                <div class="col-sm-12">
                    @include('directors.elements.messeges.oper_ok')
                    {{-- @includeif('directors.home.partials.indicadores') --}}

                    <div class="p-2">
                        <div class="h4 font-weight-bold">Indicadores Principales</div>
                        @includeIf('academicos.home.partials.labels.estudiantil')
                    </div>
                    
                    <hr>
                    
                    <div class=" border rounded p-2">
                        <div class="h4 font-weight-bold pt-2">Rendimiento Estudiantíl</div>
                        <div class="px-2">
                            @include('academicos.performances.partials.index.pestudios')
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class=" border rounded p-2">
                        <div class="h4 font-weight-bold pt-2">Áreas de Conocimiento</div>
                        <div class="px-2">
                            @include('academicos.performances/partials/index/area_conocimientos')
                        </div>
                    </div>
                    
                </div>

            </div>
        {{-- </div> --}}

    </main>

    @include('directors.home.modals.main')

@endsection
