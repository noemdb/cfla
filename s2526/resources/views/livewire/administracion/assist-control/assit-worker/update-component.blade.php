<div>

    @include('livewire.elements.messeges.oper_ok')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('livewire.administracion.assist-control.assit-worker.table.workers')
            </div>
            @if ($editModeAssitWorker)
                <div class="col">
                    @include('livewire.administracion.assist-control.assit-worker.edit')
                </div>
            @endif
        </div>
    </div>

</div>
