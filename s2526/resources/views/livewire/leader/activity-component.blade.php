<div>

    <div class="p-2">
        
        <h4 class="alert alert-secondary">

            <div class="d-flex justify-content-between">
                <div><i class="{{$icon_menus['activities'] ?? ''}} text-info pr-1" aria-hidden="true"></i><span class="font-weight-bold">Módulo Planificación Académica</span></div>
                <div><span class="text-muted font-weight-bold" style="font-size: 1rem;opacity: 0.5;">Diseñado por: Prof. Carmin Cortez</span></div>
            </div> 

            @foreach ($area_conocimientos as $item)
                <small class="text-uppercase font-weight-bold text-muted" style="font-size: 1rem"> {{$loop->iteration }} {{$item->name ?? null}} @if (! $loop->last ) || @endif</small> 
            @endforeach       
            
        </h4>
        
        <div>

            @include('livewire.leader.table.activities')
            
        </div>

    </div> 

</div>
