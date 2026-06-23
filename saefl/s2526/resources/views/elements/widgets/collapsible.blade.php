<div class="card p-0 m-0 mb-2">
    <div class="card-body p-0 m-0">
        <div class="card-header alert-secondary p-0 m-0">
            <a class="btn btn-link collapsed" data-toggle="collapse" href="#idusers_label-bodycollapse-{{ $id ?? ''}}"
                role="button" aria-expanded="false" aria-controls="idusers_label-bodycollapse"
                style="text-decoration: none;">
                {{ $title ??  ''}}
            </a>
        </div>

        <div class="collapse" id="idusers_label-bodycollapse-{{ $id ?? ''}}" style="">
            <div class="card card-body p-1 m-1 border-0">
                {{ $body ?? ''}}
            </div>
        </div>
    </div>
</div>