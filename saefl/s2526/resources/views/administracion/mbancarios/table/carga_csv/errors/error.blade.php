<ul class="list-group list-group-horizontal p-1">
    <li class="list-group-item w-75  p-1 list-group-item-{{$error['class'] ?? ''}}">{{$error['messenge'] ?? ''}}</li>
    <li class="list-group-item w-25  p-1 font-weight-bold">{{$error['value'] ?? ''}}</li>
    {{-- <li class="list-group-item w-25  p-1 font-weight-bold">{{$error['alterno'] ?? ''}}</li> --}}
</ul>
