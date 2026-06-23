<div class="continer-fluid">
    <div class="row">
        <div class="col-7">
            {{-- <h4>{{$coll_political->name}}</h4> --}}
            <div class=" pl-2">
                <dl class="mb-1 ">
                    {{-- <dt>
                        {{$list_comment['pescolar_id']}}
                        <span class="text-secondary">{{ $coll_political->pescolar->name ?? '' }}</span>
                    </dt> --}}
                    <dd>
                        <span class=" font-weight-bold">{{$list_comment['pescolar_id']}}:</span>
                        <span class="text-secondary">{{ $coll_political->pescolar->name ?? '' }}</span>
                    </dd>
                </dl>
                <dl class="mb-1 ">
                    <dd>
                        <span class=" font-weight-bold">{{$list_comment['code']}}:</span>
                        <span class="text-secondary">{{ $coll_political->code ?? '' }}</span>
                    </dd>
                </dl>
                <dl class="mb-1 ">
                    <dt>{{$list_comment['description']}}:</dt>
                    <dd class="text-secondary">{{ $coll_political->description ?? '' }}</dd>
                </dl>
                <dl class="mb-1 ">
                    <dd>
                        <span class=" font-weight-bold">Período:</span>
                        <span class="text-secondary">{{ f_date($coll_political->finicial) }}</span> - <span class="text-secondary">{{ f_date($coll_political->ffinal) }}</span>
                    </dd>
                </dl>
                {{-- <dl class="mb-1 ">
                    <dt>
                        <span class=" font-weight-bold">{{$list_comment['ffinal']}}:</span>
                        <span class="text-secondary">{{ f_date($coll_political->ffinal) }}</span>
                    </dt>
                </dl> --}}
                <dl class="mb-1 ">
                    {{-- <dt>{{$list_comment['canon']}}</dt> --}}
                    {{-- <dd class="text-secondary">{{ $coll_political->fullCanon ?? '' }}</dd> --}}
                    <dd>
                        <span class=" font-weight-bold">{{$list_comment['canon']}}:</span>
                        <span class="text-secondary">{{ $coll_political->full_canon ?? '' }}</span>
                    </dd>
                </dl>
                <dl class="mb-1 ">
                    <dd>
                        <span class=" font-weight-bold">{{$list_comment['status']}}:</span>
                        <span class="text-secondary">{{ ($coll_political->status=='true') ? 'Activa':'Desactiva' }}</span>
                    </dd>
                </dl>
                <dl class="mb-1 ">
                    <dd>
                        <span class=" font-weight-bold">{{$list_comment['status_approved']}}:</span>
                        <span class="text-secondary">{{ ($coll_political->status_approved=='true') ? 'Aprobada':'Desaprobada' }}</span>
                    </dd>
                </dl>
                <dl class="mb-1 ">
                    <dd>
                        <span class=" font-weight-bold">{{$list_comment['status_debts']}}:</span>
                        <span class="text-secondary">{{ ($coll_political->status_debts=='true') ? 'SI':'NO' }}</span>
                    </dd>
                </dl>
            </div>

        </div>
        <div class="col-5">
            <div class=" font-weight-bold">Indicadores:</div>
            <dl class="mb-1 pl-1">
                <dd class="text-secondary">
                    <span class=" font-weight-bold">Número de Niveles:</span>
                    <span>{{ $coll_political->coll_nivels->count() }}</span>
                </dd>
                <dd class="text-secondary">
                    <span class=" font-weight-bold" title="Porcentaje de mensajes enviados">% M. enviados:</span>
                    {{-- <span>{{ $coll_political->coll_nivels->count() }}</span> --}}
                </dd>
            </dl>
        </div>
    </div>
</div>
