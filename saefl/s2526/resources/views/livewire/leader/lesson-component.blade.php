<div>
    <div class="p-2">
        <h4 class="alert alert-secondary">
            Leccciones de los profesores que pertenecen a las Áreas de Conocimiento <br>
            @foreach ($area_conocimientos as $item)
                <small class="text-uppercase font-weight-bold text-muted" style="font-size: 1rem"> {{$loop->iteration }} {{$item->name ?? null}} @if (! $loop->last ) || @endif</small> 
            @endforeach          
        </h4>
        
        <div>
            @includeWhen($modeIndex,'livewire.leader.table.lessons')  
            @includeWhen($modeEdit,'livewire.leader.form.edit')  
            @includeWhen($modeShowImage,'livewire.leader.show.image')  
        </div>
    </div> 
</div>
