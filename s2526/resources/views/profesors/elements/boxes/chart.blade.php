@php
    $staus_shadow = (empty($shadow)) ? true : false;
@endphp
<div class="card h-100 border-0  {{ ($staus_shadow) ? 'shadow' : null }}   p-1 mb-2 bg-white rounded">
    <div class="card-body p-0 pt-1">
            <div class="widget-chart text-left w-100">
                <div class="progress-circle-wrapper text-center">
                    <div class="circle-progress d-inline-block circle-progress-primary">
                        <i class="{{$icon ?? 'fas fa-user-edit'}}  fa-2x text-{{$class ?? ''}}" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="widget-chart-content">
                    <div class="widget-subheading text-{{$class ?? ''}}">{{$header ?? ''}}</div>
                    <div class="widget-numbers">{{$total ?? '0'}}</div>
                    <div class="widget-subheading text-{{$class ?? ''}}">{{$title ?? ''}}</div>
                    <div class="widget-description text-success">
                        {{-- <i class="fa fa-angle-up "></i> --}}
                        <span class="pl-1 small text-{{$class ?? ''}}"><b>{{$subtitle ?? ''}}</b></span>
                    </div>
                </div>
            </div>
    </div>
</div>
