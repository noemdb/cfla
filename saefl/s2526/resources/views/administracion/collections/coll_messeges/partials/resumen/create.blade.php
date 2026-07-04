<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen - Últimos registros</p>
    <small class="px-1">

        @forelse ($coll_messeges as $coll_messege)

        {{-- 'user_id','coll_nivel_id','subject','title','subtitle','greeting','consider','sentence','waiting','footer' --}}

        @php
            $user = ($coll_messege->user) ? $coll_messege->user : null;
            $coll_nivel = ($coll_messege->coll_nivel) ? $coll_messege->coll_nivel : null;
            // $representant = ($coll_messege->representant) ? $coll_messege->representant : null;
            // $status = ($coll_messege->status) ? $coll_messege->status : null;
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
                        <dt>{{$list_comment['subject']}}</dt>
                        <dd class="text-secondary">{{ $coll_messege->subject ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['title']}}</dt>
                        <dd class="text-secondary">{{ $coll_messege->title ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['footer']}}</dt>
                        <dd class="text-secondary">{{ $coll_messege->footer ?? '' }}</dd>
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
