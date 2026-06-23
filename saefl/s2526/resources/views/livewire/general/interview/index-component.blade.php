<div>

    <div class="container-fluid alert">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3 px-1">
                <div class="d-flex justify-content-center bd-highlight mb-3">
                    <div class="p-2 bd-highlight">                        

                        <div class="form-signin">

                            <div class="text-center">
                                <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
                            </div>

                            <h1 class="text-dark text-center">Entrevista Interactiva</h1>

                            @if($interview->status)

                                <div class="card">
                                    <div class="text-center">
                                        <i class="fa fa-user-edit fa-6x text-success border rounded shadow-sm m-2 p-2" aria-hidden="true"></i>
                                        {{-- <img class="card-img-top  border rounded m-2" src="{{ asset('images/icon/interview.png') ?? null}}" alt="" style="width: 128px;"> --}}
                                    </div>
                                    <div class="card-body">
                                        <h4 class="card-title">{{$interview->name ?? null}}</h4>
                                        @if ($modeStart)
                                        <p class="card-text small text-muted">{{$interview->description ?? null}}</p>
                                        @endif
                                        @include('livewire.general.interview.assistant.main')
                                    </div>
                                </div>

                            @else

                                <div class="jumbotron border rounded shadow-sm">
                                    <h1 class="display-3 text-center">No hay entrevistas habilitadas</h1>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    <a class="btn btn-primary btn-lg ml-4" href="{{env('APP_URL_PORTAL')}}" role="button">Ir a nustro portal web</a>
                                </div>


                            @endif

                            {{-- <hr> --}}

                            @include('livewire.general.interview.footer')

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

