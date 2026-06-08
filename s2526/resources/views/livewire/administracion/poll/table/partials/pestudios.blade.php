@php $key = 'key-result-pestudios'.$poll_main->id; @endphp
<div class="card" wire:key="{{$key}}-card-poll-results">
    <div class="card-header alert-success">
        <strong>Participación por Grados/Niveles</strong> - Consulta: {{ $poll_main->name ?? null }}
    </div>
    <div class="card-body text-secondary">
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
                        <span class="badge badge-{{$pestudio->color ?? null}} float-right"><span class="h6">{{$count_answers ?? null}} / <span class=" font-weight-bold">{{$count_tokens ?? null}}</span></span></span>
                        <div class="progress mt-2" style="height: 0.8rem;">
                            <div class="progress-bar bg-{{$pestudio->color ?? null}} font-weight-bold" role="progressbar" style="width: {{$participations ?? null}}%" aria-valuenow="{{$participations ?? null}}" aria-valuemin="0" aria-valuemax="100">{{$participations ?? null}}%</div>
                        </div>
                    </div>
                    <div class="pb-2">
                        @php $grados = $pestudio->getPollGrados($poll_main->id); @endphp
                        @include('livewire.administracion.poll.table.partials.grados',['grados'=>$grados])
                    </div>

                </li>
            @endforeach
        </ul>
    </div>
</div>

