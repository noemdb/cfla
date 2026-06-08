<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen - Últimos registros</p>
    <small class="px-1">

        @forelse ($coll_activities as $coll_activity)

        {{-- 'user_id','coll_nivel_id','representant_id','description','status_id','status_messege','status_call' --}}

        @php
            $user = ($coll_activity->user) ? $coll_activity->user : null;
            $coll_nivel = ($coll_activity->coll_nivel) ? $coll_activity->coll_nivel : null;
            $representant = ($coll_activity->representant) ? $coll_activity->representant : null;
            $status = ($coll_activity->status) ? $coll_activity->status : null;
        @endphp

            <div class="row align-items-center">
                <div class="col-2 text-right h-auto">
                    <span class=" font-weight-bold">{{$loop->iteration}}</span>
                </div>
                <div class="col-10">
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['user_id']}}</dt>
                        <dd class="text-secondary">{{ ($user) ? $user->username : null}}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['coll_nivel_id']}}</dt>
                        <dd class="text-secondary">{{ ($coll_nivel) ? $coll_nivel->fullname : null}}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['representant_id']}}</dt>
                        <dd class="text-secondary">{{ ($representant) ? $representant->name : null}}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['description']}}</dt>
                        <dd class="text-secondary">{{ $coll_activity->description ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['status_id']}}</dt>
                        <dd class="text-secondary">{{ ($status) ? $status->name : null}}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['status_messege']}}</dt>
                        <dd class="text-secondary">{{ ($coll_activity->status_messege=='true') ? 'SI':'NO' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['status_call']}}</dt>
                        <dd class="text-secondary">{{ ($coll_activity->status_call=='true') ? 'SI':'NO' }}</dd>
                    </dl>
                </div>
            </div>

            <hr>
        @empty
            <div class=" font-weight-bold text-muted">
                No hay registros
            </div>
        @endforelse
    </small>
</div>
