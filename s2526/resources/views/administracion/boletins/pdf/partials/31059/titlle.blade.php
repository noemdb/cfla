<div style="font-size: 1rem; font-weight: bold;">
    Informe Evaluativo.<br>
    <span class="small d-block p-0 m-0 " style="">
        PERIODO ACADÉMICO {{ Session::get('pescolar_name') }}
    </span>
    @if (!empty($lapso_id))<span class="text-muted" style="float: right !important;">{{$lapso->name ?? ''}}</span>@endif
</div>
