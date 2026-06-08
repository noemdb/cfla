<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen - Últimos registros</p>
    <small class="px-1">

        @forelse ($coll_promises as $coll_promise)

        {{-- 'coll_political_id','representant_id','date','ammount','exchange_ammount','status','description','observation' --}}

        @php
            $coll_political = ($coll_promise->coll_political) ? $coll_promise->coll_political : null;
            $representant = ($coll_promise->representant) ? $coll_promise->representant : null;
        @endphp

            <div class="row align-items-center">
                <div class="col-2 text-right h-auto">
                    <span class=" font-weight-bold">{{$loop->iteration}}</span>
                </div>
                <div class="col-10">
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['coll_political_id' ?? '']}}</dt>
                        <dd class="text-secondary">{{ ($coll_political) ? $coll_political->username : null}}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['representant_id'] ?? ''}}</dt>
                        <dd class="text-secondary">{{ ($representant) ? $representant->name : null}}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['date'] ?? ''}}</dt>
                        <dd class="text-secondary">{{ f_date($coll_promise->date) }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['exchange_ammount'] ?? ''}}</dt>
                        <dd class="text-secondary">{{ f_float($coll_promise->exchange_ammount) }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['description'] ?? ''}}</dt>
                        <dd class="text-secondary">{{ $coll_promise->description ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['observation'] ?? ''}}</dt>
                        <dd class="text-secondary">{{ $coll_promise->observation ?? '' }}</dd>
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
