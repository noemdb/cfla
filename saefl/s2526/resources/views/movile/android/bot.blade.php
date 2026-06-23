@extends('movile.android.layouts.app')

@section('content')

        <div class="content py-2 px-1">

            <div class=" d-flex d-flex justify-content-center">
                <div>
                    <i class="bi bi-robot d-block mb-0 text-success" style="font-size: 4rem;"></i>
                    <div class="small fw-bold text-muted">Bot SAEFL</div>
                </div>
            </div>

            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="bi bi-chat-dots px-2" style="font-size: 2rem;"></i>
                            ADMINISTRACION
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body p-1">
                            @livewire('bot.index-component', ['area' => 'ADMINISTRACION'])
                        </div>
                    </div>
                </div>

                {{-- <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="bi bi-chat-dots px-2" style="font-size: 2rem;"></i>
                            CONTROL ESTUDIO
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body p-1">
                            @livewire('bot.index-component', ['area' => 'CONTROL ESTUDIO'])
                        </div>
                    </div>
                </div> --}}

            </div>

{{--
            <nav>
                <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">ADMINISTRACION</button>
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">CONTROL ESTUDIO</button>
                </div>
            </nav>
            <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">
                <div class="tab-pane fade show active  p-2" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                    @livewire('bot.index-component', ['area' => 'ADMINISTRACION'])
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                    @livewire('bot.index-component', ['area' => 'CONTROL ESTUDIO'])
                </div>
            </div> --}}

        </div>

@endsection

@section('stylesheets')
	@parent
	<link href="{{ asset('vendor/stepper/css/bs-stepper.min.css') }}" rel="stylesheet">
@endsection


@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal',function(e){
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm',function(e){

            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((result) => {
                if (result.value) {
                    window.livewire.emit('remove',e.detail.id);
                }
            });
        });

        window.addEventListener('swal:question',function(e){
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((result) => {
                if (result.value) {
                    window.livewire.emit(e.detail.method,e.detail.id);
                }
            });
        });

    </script>
@endsection
