<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen - Últimos registros</p>
    <small class="px-1">

        @forelse ($coll_politicals as $coll_political)

        {{-- 'pescolar_id','name','code','description','finicial','ffinal','status','status_approved' --}}

            @include('administracion.collections.coll_politicals.partials.details.create')

            {{-- <div class="row align-items-center">
                <div class="col-2 text-right h-auto">
                    <span class=" font-weight-bold">{{$loop->iteration}}</span>
                </div>
                <div class="col-10">
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['pescolar_id']}}</dt>
                        <dd class="text-secondary">{{ $coll_political->pescolar->name ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['name']}}</dt>
                        <dd class="text-secondary">{{ $coll_political->name ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['code']}}</dt>
                        <dd class="text-secondary">{{ $coll_political->code ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['description']}}</dt>
                        <dd class="text-secondary">{{ $coll_political->description ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['finicial']}}</dt>
                        <dd class="text-secondary">{{ f_date($coll_political->finicial) }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['ffinal']}}</dt>
                        <dd class="text-secondary">{{ f_date($coll_political->ffinal) }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['canon']}}</dt>
                        <dd class="text-secondary">{{ $coll_political->fullCanon ?? '' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['status']}}</dt>
                        <dd class="text-secondary">{{ ($coll_political->status=='true') ? 'Activa':'Desactiva' }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>{{$list_comment['status_approved']}}</dt>
                        <dd class="text-secondary">{{ ($coll_political->status_approved=='true') ? 'Aprobada':'Desaprobada' }}</dd>
                    </dl>

                </div>
            </div> --}}

            <hr>
        @empty
            <div class=" font-weight-bold text-muted">
                No hay registros
            </div>
        @endforelse
    </small>
</div>
