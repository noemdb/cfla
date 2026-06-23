@php 
    $goal_ammount = (!empty($goal_ammount)) ? $goal_ammount: 0 ;
    $actual_ammount = (!empty($actual_ammount)) ? $actual_ammount: 0 ;

    $total = ($goal_ammount>0) ? round(($actual_ammount / $goal_ammount * 100)) : 0 ;

    $class = ($total<10) ? 'success' : null ;
    $class = ($total>=10 && $total<50) ? 'info' : $class ;
    $class = ($total>=50 && $total<75) ? 'warning' : $class ;
    $class = ($total>=75) ? 'danger' : $class ;
@endphp

<div class="info-box">
    <span class="info-box-icon bg-{{$class ?? 'secondary'}} text-white">
    {{-- <i class="fas fa-archive" aria-hidden="true"></i> --}}
    {{round($total)}}<span class="small">%</span>
    </span>        
    <div class="info-box-content">
        <span class="info-box-text {{ $class_title ?? ''}}">
            <b>{{$title ?? ''}}</b> 
        </span>
        @if (!empty($subtitle))
            <span class="info-box-text small {{ $class_sub ?? ''}} "><b>{{$subtitle}}</b></span>
        @endif
        {{-- <span class="info-box-text small">Estudiantes Deudores</span> --}}
        {{-- <span class="info-box-number">{{round($total)}}<small>%</small></span> --}}
    </div>
</div>