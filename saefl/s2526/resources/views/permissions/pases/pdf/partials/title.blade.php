<h4>
    Pase Escolar.<br>
    <span class="small d-block p-0 m-0 ">
        PERIODO ACADÉMICO {{ Session::get('pescolar_name') }}
    </span>
    @if (!empty($lapso))<span class="text-muted" style="float: right !important;">{{$lapso->name ?? ''}}</span>@endif
</h4>
