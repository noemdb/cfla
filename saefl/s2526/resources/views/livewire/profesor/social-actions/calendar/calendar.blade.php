@php
    $now = $current->copy();
    $i = 1;
@endphp

{{-- {{$community_actions}} --}}

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

            @php $day_of_week = $now->dayOfWeek; $nowFormat = $now->copy()->format('Y-m-d') @endphp
            @php $events = $community_actions->where('date',$nowFormat); @endphp
            @php $is_event = ($events->isNotEmpty()) ? true : false; @endphp

            @if ($i==1) <tr> @endif
            @if ($now->format('d')=='01')
                @for ($n = 1; $n <= $day_of_week; $n++) <td> &nbsp; </td> @endfor
                @php $i=$day_of_week+1; @endphp
            @endif
            <td class="text-center">
                @if ( $is_event )

                    <a wire:click="viewDetails('{{$now}}')" wire:key="{{ Str::random()}}" class="btn btn-sm btn-info shadow-sm font-weight-bold" href="#" role="button">
                        {{$now->format('d') ?? null}}
                    </a>
                @else
                    <div>
                        {{$now->format('d')}} 
                        {{-- {{$now ?? null}}  --}}
                    </div>
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

            <button type="button" class="close" wire:click='cleanView()'> <span aria-hidden="true">×</span> </button>

            <div class="p-2 mt-2">

                @foreach ($community_action_dates as $community_action)
                    <div class="border rounded p-2 mt-2 mb-4 font-weight-bold">
                        <div>Detalles de la Actividad</div>
                        <ul class="list-group">
                            <li class="list-group-item">Título: {{$community_action->title}}</li>
                            <li class="list-group-item">Descripción: {{$community_action->description}}</li>
                            <li class="list-group-item">Lugar: {{$community_action->location}}</li>
                            @if ($community_action->image_url)
                                <img src="{{ asset($community_action->image_url) ?? null}}" class="img-fluid border rounded" alt="Imagen borrada">
                            @endif
                        </ul>
                    </div>
                @endforeach

            </div>

        </div>

@endif
