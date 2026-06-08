<div class="mb-2 fw-bold h2 text-center text-muted">Proceso de Consulta</div>

<div class="card pb-3 mb-3 p-2 shadow-sm bg-light"
    style="{{ $poll_main->image_url ? 'background-image: url(' . asset($poll_main->image_url) . '); background-repeat: no-repeat' : null }}">

    <div class="card-body px-1">
        <div class="text-end">
            <small wire:loading.delay.shortest class="text-muted small px-2">
                Procesando...
            </small>
        </div>
        <div class="h5 pb-0 text-left ">
            <span class="fw-normal small">Nombre: </span>
            <div class=" text-muted fw-bold">{{ $poll_main->name ?? null }}</div>
        </div>
        <hr class="p-1 m-1">
        <div class="fw-normal ps-2">
            {{-- <div class=" pl-3 text-secondary pb-0 small">Participante: {{ Str::limit($poll_token->email,10,'***')}}</div> --}}
            <div class=" pl-3 text-secondary pb-0 small">Participante: {{ $poll_token->email_hide ?? null }}</div>
            <div class=" pl-3 text-secondary pb-0 small">Descripción: {{ $poll_main->description ?? null }}</div>
            {{-- <div class=" pl-3 text-secondary pb-0 small">Comienzo: {{$poll_main->start->format('d-m-Y H:i:s A') ?? null}}</div> --}}
            <div class=" pl-3 text-secondary pb-0 small">Finalización:
                {{ $poll_main->end->format('d-m-Y H:i:s A') ?? null }}</div>


            <div class=" pl-3 text-secondary pb-0 small">Núm. preguntas: {{ $poll_questions->count() ?? null }}</div>
            <div class=" pl-3 text-secondary pb-0 small">Núm. respuestas registradas:
                {{ $poll_answers->count() ?? null }}</div>

            <div class="progress my-2">
                @php $valuenow = ($poll_questions->count()) ? round(100 * $poll_answers->count() / $poll_questions->count()) : null; @endphp
                <div class="progress-bar progress-bar-striped fw-bold" role="progressbar" aria-label="Basic example"
                    style="width: {{ $valuenow ?? null }}%" aria-valuenow="{{ $valuenow ?? null }}" aria-valuemin="0"
                    aria-valuemax="100">{{ $poll_answers->count() ?? null }}</div>
            </div>

            @if (Session::has('operp_ok'))
                <div class="alert alert-success alert-dismissible fade show fw-bold small" role="alert">
                    {{ Session::get('operp_ok') }}.
                </div>
            @endif

        </div>

        <hr class="">

        <div class="pl-1">

            <div>Preguntas:</div>


            {!! Form::select('question_id', $list_question, old('question_id'), [
                'wire:model' => 'question_id',
                'class' => 'form-select',
                'placeholder' => 'Seleccione la pregunta',
                'required' => 'required',
            ]) !!}
            @error('question_id')
                <span class="text-danger small">{{ $message }}</span>
            @enderror


            @if ($list_options)
                <hr>
                <div>Opciones:</div>
                {!! Form::select('option_id', $list_options, old('option_id'), [
                    'wire:model' => 'option_id',
                    'class' => 'form-select',
                    'placeholder' => 'Seleccione su respuesta',
                    'required' => 'required',
                ]) !!}
                @error('option_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror

                @if ($poll_option)

                    <div class="alert alert-info my-2">
                        <div>
                            <div class="fw-bold text-muted mb-2">
                                {{ $poll_option->text ?? null }} - <i class=" font-weight-normal"> <span
                                        class=" text-muted">{{ $poll_option->description ?? null }}</span></i>
                            </div>

                            @if ($poll_option->image)
                                <div class="d-flex justify-content-around">
                                    <div class="card" style="width: 18rem;">
                                        <img src="{!! asset($poll_option->image_url) !!}" class="card-img-top" alt="...">
                                        <div class="card-body">
                                            <p class="card-text">{{ $poll_option->description }}.</p>

                                        </div>
                                    </div>
                                    @if ($poll_option->body)
                                        <div class="text-start ms-2">
                                            <div class="">{!! $poll_option->body !!}</div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>

                @endif

            @endif

            @error('status_solvent')
                <span class="text-danger small">{{ $message }}</span>
            @enderror

            <hr>



            <div class="d-grid gap-2 col-6 mx-auto">
                {{-- <button type="button" class="btn btn-primary btn-lg btn-block" wire:click="save()" wire:loading.attr="disabled" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">Registrar</button> --}}
                <button type="button" class="btn btn-primary btn-lg btn-block" wire:click="save()"
                    wire:loading.attr="disabled">Registrar</button>
            </div>

        </div>

    </div>

    {{-- {{$poll_main->description ?? null}} --}}

</div>


{{-- <button type="button" class="btn btn-primary btn-lg btn-block" wire:click="store()">Registrar</button> --}}
{{-- <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example"> --}}
{{-- {!! Form::button('Registrar',['class' => 'form-control btn btn-primary btn-lg','wire:click'=>"store()"]) !!} --}}
{{-- </div> --}}
