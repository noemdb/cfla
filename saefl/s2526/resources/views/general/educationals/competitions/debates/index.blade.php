@extends('general.layaout.main')

@section('title') U.E. Colegio Fray Luis Amigó @endsection

@section('main')

    <main role="main">

        <div>

            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm12 px-1">

                        <div class="border rounded">

                            <div class="p-2">
                                <div class="text-center">
                                    <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
                                </div>
                                <h2 class="text-dark text-center">
                                    <div>
                                        <span class="fw-lighter">Competición:</span>
                                        <div class="fw-bold">{{$competition->name}}</div>
                                    </div>                                                                            
                                </h2>
                                <hr>
                                <h4 class="text-center text-secondary">
                                    <div class="fw-light">Registro de Preguntas para:</div>
                                    <div class="fw-bold">{{$debate->name}}</div>
                                    <div class="fw-light small">{{$debate->description}}</div>
                                    <div class="fw-bold">{{$debate->grado->name}}</div>
                                </h4>
                            </div>

                            <hr>

                            <div class="p-2">
                                <livewire:general.educational.competition.debate.index-component :token="$debate->token"/>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
            
        </div> 

    </main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection


