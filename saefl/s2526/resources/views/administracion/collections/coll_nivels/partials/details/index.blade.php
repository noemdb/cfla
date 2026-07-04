<div class="row align-items-center">
    <div class="col-2 text-right h-auto">
        <span class=" font-weight-bold">{{$loop->iteration}}</span>
    </div>
    <div class="col-10">
        <dl class="mb-1 ">
            <dt>{{$list_comment['coll_political_id']}}</dt>
            <dd class="text-secondary">{{ $coll_nivel->coll_political->fullname ?? '' }}</dd>
            {{-- <dd class="text-secondary">{{ $coll_nivel->coll_political ?? '' }}</dd> --}}
        </dl>
        <dl class="mb-1 ">
            <dt>{{$list_comment['name']}}</dt>
            <dd class="text-secondary">{{ $coll_nivel->name ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>{{$list_comment['order']}}</dt>
            <dd class="text-secondary">{{ $coll_nivel->order ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>{{$list_comment['weighing']}}</dt>
            <dd class="text-secondary">{{ $coll_nivel->weighing ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>{{$list_comment['description']}}</dt>
            <dd class="text-secondary">{{ $coll_nivel->description ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>{{$list_comment['status']}}</dt>
            <dd class="text-secondary">{{ ($coll_nivel->status=='true') ? 'Activo':'Desactivo' }}</dd>
        </dl>
    </div>
</div>
