<div>

    <div class="container-fluid alert">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3 px-1">
                <div class="d-flex justify-content-center bd-highlight mb-3">
                    <div class="p-2 bd-highlight">

                        {{-- <h1 class="text-danger text-center">Prueba de funcionamiento. </h1> --}}

                        <div class="form-signin">

                            <div class="text-center">
                                <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144"
                                    height="144">
                            </div>

                            @if($poll_main->status_active)

                                @if ($status_ready)

                                    @include('livewire.general.poll.partials.ready')

                                @else

                                    @include('livewire.general.poll.partials.questions')

                                @endif

                            @else

                                @include('livewire.general.poll.partials.oftime')

                            @endif

                            <hr>

                            @include('livewire.general.poll.partials.info')

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @section('livewires')
    @parent
    <script>
        Livewire.on('QuestionsFocus', () => {
                const element = document.getElementById("question-selected");
                element.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
                console.log('QuestionsFocus');
            })
    </script>
    @endsection

</div>