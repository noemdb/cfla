<div class="container-fluid py-4">
    <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center sticky-top border-bottom">
            <h5 class="mb-0 font-weight-bold text-secondary">
            @foreach($peducativos as $peducativo)
                @foreach($peducativo->grados as $grado)
                @if($grado->id == $selectedGrado)
                    {{ $grado->name }} - {{ $peducativo->name }}
                @endif
                @endforeach
            @endforeach
            </h5>
            <button wire:click="closeDetails" class="btn btn-sm btn-danger rounded">
            <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="card-body p-4">
            <div class="row">
            <!-- Correct Questions Column -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-success border-left-0 border-right-0 border-top-0 border-bottom">
                    <h6 class="mb-0 text-success font-weight-bold">Preguntas Correctas</h6>
                </div>
                <div class="card-body p-0">
                    @php
                    $QuestionsCorrects = $competition->getCorrectAnsweredQuestionsByGrado($selectedGrado);
                    $groupedCorrects = $QuestionsCorrects->groupBy('category');
                    $i = 0;
                    @endphp

                    @foreach($groupedCorrects as $category => $questions)
                    <div class="mb-3">
                        <div class="px-3 py-2 bg-light border-top">
                        <h6 class="mb-0 text-dark font-weight-bold">{{ $category }}</h6>
                        </div>
                        <div class="list-group list-group-flush pl-2">
                        @foreach($questions as $question)
                            @php $i++; @endphp
                            <div>{{$i}}</div>
                            <div class="list-group-item border-left-0 border-right-0">
                            <p class="font-weight-bold mb-1">{{ $loop->iteration }}. {!! $question->text !!}</p>
                            <div class="d-flex text-muted small mb-2">
                                <span><i class="fas fa-clock mr-1"></i>{{ $question->time }} seg</span>
                                <span class="ml-3"><i class="fas fa-trophy mr-1"></i>{{ $question->weighting }} pts</span>
                            </div>

                            <div class="list-group list-group-flush border-0">
                                @foreach($question->options as $option)
                                <div class="list-group-item py-2 px-3 border-0 {{ $option->status_option_correct ? 'bg-light' : '' }}">
                                    -. {!! $option->text !!}
                                    @if($option->status_option_correct)
                                    <i class="fas fa-check-circle text-success ml-2"></i>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                </div>
            </div>

            <!-- Wrong Questions Column -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-danger border-left-0 border-right-0 border-top-0 border-bottom">
                    <h6 class="mb-0 text-danger font-weight-bold">Preguntas Erradas</h6>
                </div>
                <div class="card-body p-0">
                    @php
                    $QuestionsWrongs = $competition->getWrongAnsweredQuestionsByGrado($selectedGrado);
                    $groupedWrongs = $QuestionsWrongs->groupBy('category');
                    $i = 0;
                    @endphp

                    @foreach($groupedWrongs as $category => $questions)
                    <div class="mb-3">
                        <div class="px-3 py-2 bg-light border-top">
                        <h6 class="mb-0 text-dark font-weight-bold">{{ $category }}</h6>
                        </div>
                        <div class="list-group list-group-flush pl-2">
                        @foreach($questions as $question)
                            @php $i++; @endphp
                            <div>{{$i}}</div>
                            <div class="list-group-item border-left-0 border-right-0">
                            <p class="font-weight-bold mb-1">{{ $loop->iteration }}. {!! $question->text !!}</p>
                            <div class="d-flex text-muted small mb-2">
                                <span><i class="fas fa-clock mr-1"></i>{{ $question->time }} seg</span>
                                <span class="ml-3"><i class="fas fa-trophy mr-1"></i>{{ $question->weighting }} pts</span>
                            </div>

                            <div class="list-group list-group-flush border-0">
                                @foreach($question->options as $option)
                                <div class="list-group-item py-2 px-3 border-0 {{ $option->status_option_correct ? 'bg-light font-weight-bold' : '' }}">
                                    -.{!! $option->text !!}
                                </div>
                                @endforeach
                            </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>