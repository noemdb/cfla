<span class="px-2 pt-1">
    <span class="font-weight-bold text-muted">Participación por Sección</span>
</span>

<div class="card">
    <ul class="list-group list-group-flush">
        @foreach($seccions as $seccion)
            @php
                $poll_answers = $seccion->getPollAnswers($poll_main->id);
                $count_answers = $poll_answers->count();

                $poll_tokens = $seccion->getPollTokens($poll_main->id);
                $count_tokens = $poll_tokens->count();

                $participations = ($count_tokens) ? 100 * $count_answers / $count_tokens : null;
                $participations = round($participations,2);
            @endphp
            <li class="list-group-item py-1">
                <div class=" d-flex justify-content-between align-items-center">
                    <div class=" font-weight-bold text-muted">Sección {{ $seccion->name ?? null}}</div>
                    <div>
                        <span class="text-muted small border rounded p-2 text-success">
                            <span class="">Participantes: {{$count_answers ?? null}} || </span> <span class="font-weight-bold">Invitaciones: {{$count_tokens ?? null}}</span>
                        </span>
                    </div>
                </div>
                <div class="progress mt-0" style="height: 0.8rem;">
                    <div class="progress-bar bg-{{$grado->color ?? null}} font-weight-bold" role="progressbar" style="width: {{$participations ?? null}}%" aria-valuenow="{{$participations ?? null}}" aria-valuemin="0" aria-valuemax="100">{{$participations ?? null}}%</div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
