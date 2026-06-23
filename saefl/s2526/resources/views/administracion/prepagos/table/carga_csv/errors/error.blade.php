<ul class="list-group list-group-horizontal">
    <li class="list-group-item w-75 list-group-item-{{$error['class'] ?? ''}}">{{$error['messenge'] ?? ''}}</li>
    <li class="list-group-item w-25 font-weight-bold">{{$error['value'] ?? ''}}</li>
</ul>
