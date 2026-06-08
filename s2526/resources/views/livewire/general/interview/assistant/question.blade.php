
@if ($question->id)
    <div class="alert alert-secondary" role="alert">
        <strong>Representante:</strong> <span>{{$representant->name ?? null}}</span>
    </div>

    @php
        $count = $answeredQuestions->count() + 1;
        
        $total = $questions->count();
        $width = ($total) ? round((100*$count/$total),2) : null;
    @endphp
    <div class="progress my-4">
        <div class="progress-bar progress-bar-striped" role="progressbar" aria-label="Example 1px high" style="width: {{$width}}%;" aria-valuenow="{{$width}}" aria-valuemin="0" aria-valuemax="100">
            {{$count}} / {{$total}}
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">{{$question->text ?? null}}</h4>
            <small class="text-muted small">{{$question->observations ?? null}}</small>
            <p class="card-text">
                @include('livewire.general.interview.assistant.answer')
            </p>
        </div>
    </div>

    
@else
    <div class="jumbotron">
        <h1 class="display-3 text-center">Todas las preguntas han sido contestadas.</h1>
    </div>

    <a class="btn btn-success" type="button" href="#" wire:click="goToStart" id="redirect">ir al Inicio</a>
    
@endif



