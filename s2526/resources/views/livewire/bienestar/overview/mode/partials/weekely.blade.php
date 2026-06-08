@php
    $now = $current->copy();
    $i = 1;
    // $incidents = $profesor->incidents; //dd($incidents);
    
    $incidents = $profesor->incidents_day; //dd($incidents);
@endphp


<div class="border rounded shadow-sm">

    <div class="d-flex justify-content-between mb-2 p-1">
        <div class="btn-group btn-group-sm border rounded w-25">
            <button type="button" class="btn btn-light btn-sm" wire:click='lastYear()'> <span class="font-weight-bold" aria-hidden="true"><i class="fas fa-angle-left"></i></span> </button>
            <button type="button" class="btn btn-light btn-sm"> <span class="font-weight-bold" aria-hidden="true">{{$current->year}}</span> </button>
            <button type="button" class="btn btn-light btn-sm" wire:click='nextYear()'> <span class="font-weight-bold" aria-hidden="true"><i class="fas fa-angle-right"></i></span> </button>
        </div>
        <div class="btn-group btn-group-sm border rounded w-25">
            <button type="button" class="btn btn-light btn-sm" wire:click='lastMonth()'> <span class="font-weight-bold" aria-hidden="true"><i class="fas fa-angle-left"></i></span> </button>
            <button type="button" class="btn btn-light btn-sm"> <span class="font-weight-bold text-capitalize" aria-hidden="true">{{$current->monthName}}</span> </button>
            <button type="button" class="btn btn-light btn-sm" wire:click='nextMonth()'> <span class="font-weight-bold" aria-hidden="true"><i class="fas fa-angle-right"></i></span> </button>
        </div>
    </div>

    <table width="100%" class="table table-striped table-hover table-sm small p-1">
        <tr>
            <th class="text-center">Domingo</th>
            <th class="text-center">Lunes</th>
            <th class="text-center">Martes</th>
            <th class="text-center">Miercóles</th>
            <th class="text-center">Jueves</th>
            <th class="text-center">Viernes</th>
            <th class="text-center">Sábado</th>
        </tr>

        @while ( $now->lte($end))

            @php
                $day_of_week = $now->dayOfWeek;

                $now_sm = $now->copy()->format('Y-m-d');
                $incidents = $profesor->getIncidentsDay($now_sm);
                $is_event = ($incidents->isNotEmpty()) ? true : false;                
                
                $finicial = $now->copy()->startOfWeek()->format('Y-m-d');
                $ffinal = $now->copy()->endOfWeek()->format('Y-m-d');
                $eWeekelys = $profesor->getIncidentsRange($finicial,$ffinal);
                $is_weekelys = ( $eWeekelys->isNotEmpty() ) ? true : false ;
                
            @endphp

            @if ($i==1) 

                @if ($is_weekelys)
                    <tr wire:click="viewDetailsRange('{{$finicial}}','{{$ffinal}}')" class="table-danger" style="cursor: pointer">
                @else
                    <tr>
                @endif

            @endif
            @if ($now->format('d')=='01')
                @for ($n = 1; $n <= $day_of_week; $n++) <td> &nbsp; {{$now}} </td> @endfor
                @php $i=$day_of_week+1; @endphp
            @endif
            <td class="text-center" style="vertical-align: middle">
                @if ( $is_event )
                    <div class="btn btn-sm btn-info shadow-sm font-weight-bold" style="cursor: pointer">{{$now->format('d') ?? null}}</div>
                @else
                    <div>{{$now->format('d')}}</div>
                @endif
            </td>
            @if ($i==7) </tr> @php $i=0; @endphp @endif

            @php $i++; $now = $now->addDay(); @endphp

        @endwhile

    </table>

</div>

@if ($modeDetails)

        <hr>

        <div class="p-1 border rounded shadow-sm">

            <button type="button" class="close" wire:click='closeDetails()'> <span aria-hidden="true">×</span> </button>

            <div class="p-2 mt-2">

                <div class=" font-weight-bolder border-bottom py-2 h5 mb-0 text-primary">Detalles de las incidencias</div>

                @foreach ($incidentsWeekelys as $incident)
                    <div class="border rounded p-2 mt-2 mb-4 font-weight-bold">
                        <div class="d-flex flex-row">

                            <div class="h4 mb-0 font-weight-bold text-center d-flex align-items-center border-right pr-2">{{ $loop->iteration}}</div>
                            <div class="pl-2 w-100">@include('livewire.bienestar.overview.mode.partials.incident_ul')</div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>

@endif
