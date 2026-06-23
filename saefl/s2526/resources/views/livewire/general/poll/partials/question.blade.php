@forelse ($poll_questions as $poll_question)

    @php $list_options = $poll_question->list_options; @endphp

    <div class="text-left">
        <span class="fw-bold"> Pregunta {{ $loop->iteration ?? null }}:</span>
        <div class="text-dark">

            <div> {{ $poll_question->text ?? null }} </div>

            @if ($poll_question->body)
                <div class="">
                    <div class="text-secondary">
                        <div class="">Detalles:</div>
                        <div class="pl-1">{!! $poll_question->body ?? null !!}</div>
                    </div>
                </div>
            @endif

            {!! Form::select('poll_option_id', $list_options, old('poll_option_id'), [
                'wire:model.defer' => 'poll_option_id',
                'wire:change' => 'loadQuestionId()',
                'class' => 'form-select',
                'id' => 'poll_option_id',
                'placeholder' => 'Seleccione su respuesta',
                'required' => 'required',
            ]) !!}
            @error('poll_option_id')
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
                                    <img src="{!! asset($poll_option->image) !!}" class="card-img-top" alt="...">
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
        </div>
    </div>

@empty
    <p class="mt-5 mb-3 text-muted">No hay Preguntas</p>
@endforelse
