@php $class_progress = ($total<25) ? 'danger' : null ; $class_progress = ($total>=25 && $total<50) ? 'warning' : $class_progress ; $class_progress = ($total>=50 && $total<90) ? 'info' : $class_progress ; $class_progress = ($total>=90) ? 'success' : $class_progress ; @endphp

<div class="card h-100  border rounded shadow ml-2">
{{-- <div class="card h-100 border-0 ml-2 "> --}}
    <div class="card-body p-0 pt-1">
        <div class="widget-chart text-left w-100">
            @if (!empty($icon))
                <div class="progress-circle-wrapper text-center">
                    <div class="circle-progress d-inline-block circle-progress-primary">
                        <i class="{{$icon ?? 'fas fa-user-edit'}} fa-2x text-{{$class ?? ''}}" aria-hidden="true"></i>
                    </div>
                </div>
            @endif

            <div class="widget-chart-content">
                <div class="widget-subheading text-{{$class ?? ''}}">{{$header ?? ''}}</div>
                <div class="widget-numbers {{$class_total ?? ''}}">{!! $total ?? '' !!}{!! $unidad ?? '' !!}</div>
                <div class="widget-subheading text-{{$class ?? ''}}">{{$title ?? ''}}</div>
                <div class="widget-description text-success">
                    {{-- <i class="fa fa-angle-up "></i> --}}
                    <span class="pl-1 small text-{{$class ?? ''}}"><b>{{$subtitle ?? ''}}</b></span>
                </div>
            </div>
        </div>
    </div>
    @if (!empty($progressbar))
        <div class="progress" style="height: 5px;">
            <div class="progress-bar bg-{{$class_progress ?? 'secondary'}}" style="width: {{$total ?? '0'}}%;" aria-valuenow="{{$total ?? '0'}}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    @endif
</div>

{{-- <div class="card mb-3 widget-chart text-left w-100">
    <div class="progress-circle-wrapper text-center">
        <div class="circle-progress d-inline-block circle-progress-primary">
            <i class="fas fa-user-edit fa-4x text-dark" aria-hidden="true"></i>
        </div>
    </div>
    <div class="widget-chart-content">
        <div class="widget-subheading">{{$title ?? ''}}</div>
        <div class="widget-numbers">{{$total ?? ''}}</div>
        <div class="widget-description text-success">
            <i class="fa fa-angle-up "></i>
            <span class="pl-1">{{$subtitle ?? ''}}</span>
        </div>
    </div>
</div>      --}}
