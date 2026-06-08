@php
    $goal_ammount = (!empty($goal_ammount)) ? $goal_ammount: 0 ;
    $actual_ammount = (!empty($actual_ammount)) ? $actual_ammount: 0 ;
    $width = ($goal_ammount>0) ? round(($actual_ammount / $goal_ammount * 100)) : 0 ;

    $class = ($width<25) ? 'danger' : null ;
    $class = ($width>=25 && $width<50) ? 'warning' : $class ;
    $class = ($width>=50 && $width<90) ? 'info' : $class ;
    $class = ($width>=90) ? 'success' : $class ;

@endphp

<div class="info-box">
  <span class="info-box-icon bg-{{$class ?? 'secondary'}} text-white">
  {{-- <i class="fas fa-archive" aria-hidden="true"></i> --}}
    {{$width ?? ''}}<span class="small">%</span>
  </span>        
  <div class="info-box-content">
      <span class="info-box-text">
          <b>{{$title ?? ''}}</b>
      </span>
      {{-- <span class="float-right small text-muted"><b>@readable_int($goal_ammount)</b>/@readable_int($actual_ammount)</span> --}}
      
      <span class="info-box-text">
        <div class="progress-group p-0 pl-2 pr-2">
          {{-- <span class="float-right small text-muted"><b>@readable_int($goal_ammount)</b>/@readable_int($actual_ammount)</span> --}}
          {{-- <span class="small text-muted">{{$title ?? ''}}</span>         --}}
          <div class="progress pb-0 pt-0 mb-0 mt-0" style="height:{{$height ?? '10'}}px">      
            <div class="progress-bar bg-{{$class ?? 'secondary'}} progress-bar-striped" style="width: {{$width ?? '0'}}%;" aria-valuenow="{{$width ?? '0'}}" aria-valuemin="0" aria-valuemax="100">
                {{-- <small>{{$width ?? '0'}}%</small> --}}
            </div>
          </div>
          <span class="small pb-1">
            <span class="float-right small text-secondary">            
                <b>@readable_int($goal_ammount)</b>/@readable_int($actual_ammount)            
            </span>
          </span>
        </div>
      </span>
      {{-- <span class="info-box-number">
          <small>
            <b>@readable_int($goal_ammount)</b>/@readable_int($actual_ammount)
          </small>
      </span> --}}
  </div>
</div>


