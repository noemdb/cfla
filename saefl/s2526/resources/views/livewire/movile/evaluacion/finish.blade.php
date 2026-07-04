<div class="p-2">

    <div class="border rounded shadow-sm">

        <div class="text-end p-2 m-1">
            <button type="button" class="btn-close border rounded bg-secondary" data-bs-dismiss="alert" aria-label="Close" wire:click="close"></button>
        </div>

        <div class="text-muted border-bottom">{{ ($pevaluacion) ? $pevaluacion->microname : null }}</div>

        <ul class="list-group list-group-flush small">
            @forelse ($evaluacions as $item)
            <li class="list-group-item {{ ($item->status_execution) ? 'list-group-item-success fw-bold' : null}}">
                <div class="d-flex justify-content-between">
                    <div class="px-1 text-start">{{$item->description ?? null}}</div>
                    <div class="px-1 ">
                        <fieldset {{ ($item->status_execution) ? 'disabled' : null}}> 
                        <button class="btn btn-success btn-sm" wire:click="mark({{$item->id}})">
                            <i class="{{$icon_menus['check'] ?? ''}}" aria-hidden="true"></i>
                        </button>
                        </fieldset>
                    </div>
                </div>
                
            </li>
            @empty
            <li class="list-group-item disabled">No hay</li>
            @endforelse
        </ul>
        
    </div>

</div>