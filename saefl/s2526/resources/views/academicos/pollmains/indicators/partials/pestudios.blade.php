@php $pestudios = $poll_main->pestudios; @endphp

<div class="card-header alert-success">
    <strong>Participación por Grados/Niveles</strong>
</div>
<ul class="list-group">
    @foreach($pestudios as $pestudio)
        @php
            $poll_answers = $pestudio->getPollAnswers($poll_main->id);
            $count_answers = $poll_answers->count();

            $poll_tokens = $pestudio->getPollTokens($poll_main->id);
            $count_tokens = $poll_tokens->count();

            $participations = ($count_tokens) ? 100 * $count_answers / $count_tokens : null;
            $participations = round($participations,2);
        @endphp
        <li class="list-group-item">
            {{-- {{$poll_answers}} --}}
            <div class=" font-weight-bold text-muted alert alert-{{$pestudio->color ?? null}} mb-0">
                <span>{{ $pestudio->name ?? null}}</span>
                <span class="float-right">
                    <span class="h6">Participantes: {{$count_answers ?? null}} || </span> <span class="h6 font-weight-bold">Invitaciones: {{$count_tokens ?? null}}</span>
                </span>
                <div class="progress mt-2" style="height: 0.8rem;">
                    <div class="progress-bar bg-{{$pestudio->color ?? null}} font-weight-bold" role="progressbar" style="width: {{$participations ?? null}}%" aria-valuenow="{{$participations ?? null}}" aria-valuemin="0" aria-valuemax="100">{{$participations ?? null}}%</div>
                </div>
            </div>
            <div class="pb-2">
                @php $grados = $pestudio->getPollGrados($poll_main->id); @endphp
                @include('administracion.polls.indicators.partials.grados')
            </div>

        </li>
    @endforeach
</ul>