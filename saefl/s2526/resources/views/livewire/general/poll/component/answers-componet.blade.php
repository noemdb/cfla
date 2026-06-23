<div>
    <div class=" pl-3 text-secondary pb-0 small">Núm. preguntas: {{$poll_questions->count() ?? null}}</div>
    <div class=" pl-3 text-secondary pb-0 small">Núm. respuestas registradas: {{$poll_answers->count() ?? null}}</div>

    <div class="progress my-2">
        @php $valuenow = ($poll_questions->count()) ? round(100 * $poll_answers->count() / $poll_questions->count()) : null; @endphp
        <div class="progress-bar progress-bar-striped fw-bold" role="progressbar" aria-label="Basic example" style="width: {{$valuenow ?? null}}%" aria-valuenow="{{$valuenow ?? null}}" aria-valuemin="0" aria-valuemax="100">{{$poll_answers->count() ?? null}}</div>
    </div>

    @if (Session::has('operp_ok'))
        <div class="alert alert-success alert-dismissible fade show fw-bold small" role="alert">
            {{Session::get('operp_ok')}}.
        </div>
    @endif
</div>
