@php
    $class = ($width<25) ? 'danger' : null ;
    $class = ($width>=25 && $width<50) ? 'warning' : $class ;
    $class = ($width>=50 && $width<90) ? 'info' : $class ;
    $class = ($width>=90) ? 'success' : $class ;
@endphp

<div class="py-1 px-0">

    <span class="text-muted pb-0">
        {{$title ?? ''}}
    </span>

    <div class="progress pb-0 pt-0 mb-0 mt-0 align-middle" style="height:{{$height ?? '0.2rem'}}">
        <div class="progress-bar bg-{{$class ?? 'secondary'}} progress-bar-striped" style="width: {{$width ?? '0'}}%;" aria-valuenow="{{$width ?? '0'}}" aria-valuemin="0" aria-valuemax="100">
        </div>
    </div>
</div>

