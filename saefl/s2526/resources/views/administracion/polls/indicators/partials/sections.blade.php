@php $key = 'key-result-pestudios-grados'.$poll_main->id.'-'.$pestudio->id; @endphp
<ul class="list-group" wire:key="{{$key}}-card-poll-results">
    @foreach($grados as $grado)
        @php
            $poll_answers = $grado->getPollAnswers($poll_main->id);
            $count_answers = $poll_answers->count();

            $poll_tokens = $grado->getPollTokens($poll_main->id);
            $count_tokens = $poll_tokens->count();

            $participations = ($count_tokens) ? 100 * $count_answers / $count_tokens : null;
            $participations = round($participations,2);
        @endphp
        <li class="list-group-item">
            <div class=" d-flex justify-content-between align-items-center">
                <div class=" font-weight-bold text-muted">{{ $grado->name ?? null}}</div>
                <div>
                    {{-- <span class="badge badge-secondary border rounded p-2"><span class="h6">{{$participations ?? null}} %</span></span> --}}
                    <span class="text-muted small border rounded p-2 text-success">
                        {{-- <small> --}}
                            <span class="">Participantes: {{$count_answers ?? null}} || </span> <span class="font-weight-bold">Invitaciones: {{$count_tokens ?? null}}</span>
                        {{-- </small> --}}
                    </span>
                </div>
            </div>
            <div class="progress mt-2" style="height: 0.8rem;">
                <div class="progress-bar bg-{{$grado->color ?? null}} font-weight-bold" role="progressbar" style="width: {{$participations ?? null}}%" aria-valuenow="{{$participations ?? null}}" aria-valuemin="0" aria-valuemax="100">{{$participations ?? null}}%</div>
            </div>
        </li>
    @endforeach
</ul>

