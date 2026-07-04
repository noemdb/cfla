<div>

    <div class="p-2">

        <div class=""><strong>T.Generador/Énfasis:</strong> {{$activity->topic}}</div>
        <div class=""><strong>T.Temático/T.Indispensable:</strong> {{$activity->thematic}}</div>
        <div class=""><strong>Referentes/Ético</strong> {{$activity->references}}</div>

        <div class="p-2 {{($activity->status) ? 'table-success' : 'table-warning'}}">
            <div class="d-flex justify-content-between">
                <div class=" flex-grow-1">
                    <strong>Comentario [Jef.Área]:</strong> {{$activity->comments}} <span class="font-weight-bold {{ ($activity->status) ? 'text-success' : 'text-warning' }}">{{ ($activity->status) ? 'Aprobado' : 'En revisión' }}</span>
                </div>
                <div class="px-2">
                    <button type="button" class="btn btn-primary btn-sm" wire:click.prevent="setModeComment({{$activity->id}})">
                        <i class="{{ $icon_menus['edit'] ?? ''}} fa-1x"></i>
                    </button>
                </div>
            </div>
            
        </div>

        @includeWhen($modeComments, 'livewire.leader.partials.comments')

    </div>

</div>