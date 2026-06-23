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

                            <h2 class="text-success text-center">Renovación de Matrícula</h2>

                            <div class="fw-bold text-center h5">Asistente</div>

                            @if ($status_representant)
                                <div class="alert alert-primary">
                                    REPRESENTANTE: <br><strong>{{$representant->name ?? null}}</strong>. CI: <i>{{$representant->ci_representant ?? null}}</i>
                                </div>
                                @if ($estudiant)

                                    <div id="estudiant-selected">

                                        <div class="fw-bold text-black text-start">Estudiante {{ $this->estudiant_index ?? null }} de {{ $this->estudiants_formaly->count() }}</div>

                                        <div class="progress my-2">
                                            @php $valuenow = ($estudiants_formaly->count()) ? round(100 * $estudiant_index / $estudiants_formaly->count()) : null; @endphp
                                            <div class="progress-bar progress-bar-striped fw-bold" role="progressbar" aria-label="Basic example" style="width: {{$valuenow ?? null}}%" aria-valuenow="{{$valuenow ?? null}}" aria-valuemin="0" aria-valuemax="100">{{$estudiant_index ?? null}}</div>
                                        </div>

                                        <div class="text-muted mb-2 border-bottom">Ingrese toda la información requerida</div>

                                        <form wire:submit.prevent="save">

                                            @include('livewire.general.enrollment.elements.errors')

                                            @include('livewire.general.enrollment.form.main')

                                            @if($step==$limit)
                                            {!! Form::button('Guardar', ['class' => 'form-control btn pt-1 mt-1 btn-primary w-100', 'wire:click' => 'save()']) !!}
                                            @endif

                                        </form>
                                    </div>

                                @else

                                    <div class="alert alert-secondary" role="alert">
                                        <strong>Este vínculo ya no esta disponible. </strong>
                                        @if ($this->estudiants_formaly->count() > 0)
                                            <div class="text-danger text-muted fw-light">Se han registrado todas las solicitudes de renovación de matrícula asociadas a este representante.</div>
                                        @endif
                                    </div>

                                @endif


                            @else

                                <div class="alert alert-secondary" role="alert">
                                    <strong>Este vínculo no está asociado con ningún representante.</strong>
                                </div>

                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @section('livewires')
        @parent
        <script>
            Livewire.on('EstudiantFocus', () => {
                const element = document.getElementById("estudiant-selected");
                element.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
                console.log('EstudiantFocus');
            })
        </script>
    @endsection

</div>
