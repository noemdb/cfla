<div>

    <div class="p-1 border rounded shadow-lg">
 
        <div class="text-end p-1 m-1">
            <button type="button" class="btn-close p-1 border rounded bg-secondary" data-bs-dismiss="alert" aria-label="Close" wire:click="close()"></button>
        </div>        

        <div class="alert alert-secondary fw-bold text-muted small">{{($lapso) ? $lapso->name : null}}</div>

        <form wire:submit.prevent="saveLesson" class="text-start  p-2 m-2">

            @if ($pevaluacion)
                <small class="fw-bold">PLan de Evaluación</small>
                <div class="alert alert-secondary" role="alert">
                    <strong>{{$pevaluacion->asignatura_name}}</strong>
                    
                    <div class="text-muted p-2 mx-2 mt-2 border rounded">
                        <div class="small fw-light">Lecciones registradas:</div>
                        @forelse ($items as $lesson)
                            <div class="px-2 small fw-light text-truncate">{{$lesson->order ?? null}}. {{$lesson->content ?? null}}</div>
                        @empty
                            <div class="small fw-light text-center fst-italic">No hay lecciones registradas</div>            
                        @endforelse
                    </div>

                </div>

                @include('livewire.movile.profesor.learning.form.lesson')

                <button type="submit" class="btn btn-primary btn-block w-100">Guardar</button>
            @endif            
            
        </form>

    </div>
    
</div>
