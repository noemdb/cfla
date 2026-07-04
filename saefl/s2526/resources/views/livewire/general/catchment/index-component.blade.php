<div>
    
    <div id="main" class="{{ (! $status_start) ? 'blurred' : null }}">

        <div class="container-fluid alert" id="continer" style="{{ ($status_start) ? 'display: block' : 'display: none' }}">
            <div class="row">
                {{-- <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3 px-1"> --}}
                <div class="col-12 px-1">
                    <div class="d-flex justify-content-center bd-highlight mb-3">
                        <div class="p-2 bd-highlight">

                            <div class="form-signin">

                                @if ($status_intern)
                                    <h2 class="text-center text-success fw-bold">{{$institucion->name}}</h2>
                                    <h3 class="text-success text-center">Proceso Matriculación Escolar<br>2024 2025</h3>
                                    <div class="text-center">
                                        <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
                                    </div>

                                    <hr class="py-0 my-0">
                                    <hr class="py-0 my-0">
                                @endif                                                                

                                @if (empty($token))

                                    @if ($status_start)

                                        <div class="pt-2">
                                            <div class="fw-bold text-center h5 pb-0 mb-0 mt-2"><span class="fw-bold">Fase 1. Registro Inicial</span> </div>
                                            <div class="pt-0 text-center">Manifiesto mi interés para optar a este proceso de matriculación escolar.</div>
                                            <hr>
                                            @include('livewire.general.catchment.form.token')
                                        </div> 
                                        
                                    @else
                                        <div class="h1 text-center text-success-emphasis">¡Bienvenidos!</div>

                                        @include('livewire.general.catchment.partials.intro2')

                                        @include('livewire.general.catchment.partials.footer')

                                        <button class="btn btn-primary btn-lg w-100 py-4 my-4 fw-bolder" wire:click="start()">Iniciar</button>

                                    @endif
                                    
                                @else
                                    <div class="alert alert-success p-4 my-2 h-100" style="height: 50rem" role="alert">
                                        <div>
                                            <span class="fw-bold">La Fase 1</span> <span>correspondiente al registro de su interés por optar a este proceso de matriculación escolar, ha sido exitoso.</span>
                                        </div>
                                        <hr>
                                        <p>
                                            En la dirección de correo ingresada, tendrá los pasos siguientes.
                                        </p>
                                    </div>
                                    <hr>
                                    <div class="btn-group w-100" role="group">
                                        {{-- <a class="btn btn-primary btn-sm " href="{{route('catchments.index')?? null}}" role="button">Nuevo Registro</a> --}}
                                        <a class="btn btn-primary btn-sm " wire:click="restart()" href="#" role="button">Nuevo Registro</a>
                                        {{-- <a class="btn btn-dark btn-sm " href="{{env('APP_URL_PORTAL') ?? null}}" role="button">Ir a la página principal</a> --}}
                                    </div>
                                @endif                                                     

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @if (! $status_start)

            <div class="container" id="divPromo" style="z-index: 100">
                @include('livewire.general.catchment.partials.video')
            </div>

            @section('scripts') 
                @parent 
                <script>

                    const video = document.querySelector("video");
                    const divPromo = document.querySelector("#divPromo"); //console.log(video); console.log(divPromo);
                    const continer = document.querySelector("#continer");
                    const main = document.querySelector("#main");

                    video.addEventListener("ended", function() {
                        video.style.display = "none";
                        divPromo.style.display = "none";
                        continer.style.display = "block";
                        main.style.backgroundColor = "#fff";
                        main.classList.remove("blurred");
                    });
                
                    function resizeVideo() {
                    videoPromo.width = window.innerWidth;
                    videoPromo.height = window.innerHeight;
                    }        
                    window.addEventListener("resize", resizeVideo);        
                    resizeVideo();
                    videoPromo.style.display = "block";
                </script>
            @endsection

        @endif

    {{-- fin --}}
    </div>

    @section('stylesheet') 
        @parent
        <style>

            .blurred {
                margin: 0;
                height: 100vh;
                font-weight: 100;
                background: radial-gradient(#1e5725,#050e08);
                -webkit-overflow-Y: hidden;
                -moz-overflow-Y: hidden;
                -o-overflow-Y: hidden;
                overflow-y: hidden;
                -webkit-animation: fadeIn 1 1s ease-out;
                -moz-animation: fadeIn 1 1s ease-out;
                -o-animation: fadeIn 1 1s ease-out;
                animation: fadeIn 1 1s ease-out;
            }

        </style>
    @endsection

</div>
